<?php
// Copyright 2011 JMB Software, Inc.
//
// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
//
//    http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.

define('HTTP_SCHEME_HTTP', 'http');
define('HTTP_SCHEME_HTTPS', 'https');
define('HTTP_METHOD_POST', 'POST');
define('HTTP_METHOD_GET', 'GET');


class HTTP
{

    var $allow_redirection = true;

    var $max_redirects = 5;

    var $redirects = 0;

    var $max_http_code = 400;

    var $start_url = null;

    var $request_headers = null;

    var $post_data = null;

    var $response_headers = null;

    var $response_full_status = null;

    var $response_status = null;

    var $response_status_code = null;

    var $response_location = null;

    var $body;

    var $connect_timeout = 15;

    var $read_timeout = 30;

    var $error = null;

    function HTTP()
    {
    }

    function GET($url, $referrer = null, $allow_redirect = true)
    {
        $this->_reset($url, $allow_redirect);
        return $this->_request($url, HTTP_METHOD_GET, $referrer);
    }

    function POST($url, $data = array(), $referrer = null)
    {
        $this->_reset($url, $allow_redirect);
        return $this->_request($url, HTTP_METHOD_POST, $referrer, $data);
    }

    function _request($url, $method = HTTP_METHOD_GET, $referrer = null, $data = array())
    {
        $result = false;

        if( ($parsed_url = parse_url($url)) !== false )
        {
            $scheme = strtolower($parsed_url['scheme']);
            $hostname = $parsed_url['host'];
            $port = $parsed_url['port'];

            // Resolve hostname
            $ip_address = gethostbyname($hostname);
            if( $ip_address != $hostname )
            {
                // Set default HTTP port
                if( empty($port) )
                {
                    $port = $scheme == HTTP_SCHEME_HTTPS ? 443 : 80;
                }

                // SSL connection
                if( $scheme == HTTP_SCHEME_HTTPS )
                {
                    $ip_address = 'ssl://' . $ip_address;
                }

                // Open the connection
                if( ($socket = @fsockopen($ip_address, $port, $errno, $errstr, $this->connect_timeout)) !== false )
                {
                    // Send the request
                    fwrite($socket, $this->_generate_request($parsed_url, $method, $referrer, $data));

                    stream_set_timeout($socket, $this->read_timeout);

                    // Read the response
                    $response = null;
                    $read_success = true;
                    while( !feof($socket) )
                    {
                        $chunk = fread($socket, 65536);

                        if( $chunk === false )
                        {
                            $read_success = false;
                            break;
                        }

                        $response .= $chunk;
                    }
                    fclose($socket);

                    if( $read_success )
                    {
                        $this->_process_response($url, $response);

                        if( $this->redirects < $this->max_redirects )
                        {
                            if( $this->response_status_code < $this->max_http_code )
                            {
                                if( !empty($this->response_location) )
                                {
                                    return $this->_request($this->response_location, $method, $referrer, $data);
                                }
                                else
                                {
                                    $result = true;
                                }
                            }
                            else
                            {
                                $this->error = 'The URL returned HTTP status [' . $this->response_status . ']';
                            }
                        }
                        else
                        {
                            $this->error = 'URL generates too many redirects';
                        }
                    }
                    else
                    {
                        $this->error = 'Receive from remote server failed';
                    }
                }
                else
                {
                    $this->error = 'Could not connect to remote host [' . $errstr . ']';
                }
            }
            else
            {
                $this->error = 'Could not resolve hostname';
            }
        }
        else
        {
            $this->error = 'The URL is not properly formatted';
        }

        return $result;
    }

    function _reset($url, $allow_redirect)
    {
        $this->request_headers = null;
        $this->response_headers = null;
        $this->start_url = $url;
        $this->error = null;
        $this->allow_redirection = $allow_redirect;
        $this->max_http_code = $this->allow_redirection ? 400 : 300;
        $this->body = null;
        $this->redirects = 0;
        $this->response_full_status = null;
        $this->response_status = null;
        $this->response_status_code = null;
        $this->response_location = null;
    }

    function _process_response($url, $response)
    {
        $crlfx2 = "\r\n\r\n";
        $first_crlfx2 = strpos($response, $crlfx2);
        $headers = substr($response, 0, $first_crlfx2 + strlen($crlfx2));
        $this->body = substr($response, $first_crlfx2 + strlen($crlfx2));

        $this->response_headers .= $headers;

        if( preg_match('~HTTP/\d\.\d ((\d+).*)~mi', $headers, $matches) )
        {
            $this->response_full_status = trim($matches[0]);
            $this->response_status = trim($matches[1]);
            $this->response_status_code = trim($matches[2]);
        }

        $this->response_location = null;
        if( preg_match('~Location:\s+(.*)~mi', $headers, $matches) )
        {
            $this->redirects++;
            $this->response_location = $this->_relative_to_absolute($url, trim($matches[1]));
        }
    }

    function _generate_request($parsed_url, $method = HTTP_METHOD_GET, $referrer = null, $data = array())
    {
        $crlf = "\r\n";

        $uri = (isset($parsed_url['path']) ?  str_replace(' ', '%20', $parsed_url['path']) : '/' ) .
               (isset($parsed_url['query']) ? "?{$parsed_url['query']}" : '');

        // Generate POST data
        if( $method == HTTP_METHOD_POST )
        {
            $post_parts = array();
            foreach( $data as $key => $val )
            {
                $post_parts[] = "$key=" . urlencode($val);
            }

            $this->post_data = join('&', $post_parts);
        }

        // Generate request headers
        $request = "$method $uri HTTP/1.0$crlf" .
                   "Host: {$parsed_url['host']}$crlf" .
                   "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/532.5 (KHTML, like Gecko) Chrome/4.0.249.43 Safari/532.5$crlf" .
                   "Accept: application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5$crlf" .
                   "Accept-Language: en-US,en;q=0.8$crlf" .
                   "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.3$crlf" .
                   (!empty($referrer) ? "Referer: $referrer$crlf" : '') .
                   ($method == HTTP_METHOD_POST ? "Content-Length: " . strlen($this->post_data) . "$crlf" . "Content-Type: application/x-www-form-urlencoded$crlf" : '') .
                   "Connection: close$crlf$crlf";

        // Store request headers
        $this->request_headers .= $request;

        // Add post data to the request
        if( $method == HTTP_METHOD_POST )
        {
            $request .= $this->post_data;
        }

        return $request;
    }

    function _relative_to_absolute($start_url, $relative_url)
    {
        if( empty($relative_url) )
        {
            return $start_url;
        }
        else if( preg_match('~^https?://~i', $relative_url) )
        {
            return $relative_url;
        }

        $parsed = parse_url($start_url);
        $base_url = $parsed['scheme'] . '://' . $parsed['host'] . (isset($parsed['port']) ? ':' . $parsed['port'] : '');

        if( $relative_url[0] == '/' )
        {
            return $base_url . $this->_resolve_path($relative_url);
        }
        else
        {
            // Strip filename from path
            $parsed['path'] = preg_replace('~[^/]+$~', '', $parsed['path']);

            return $base_url . $this->_resolve_path($parsed['path'] . $relative_url);
        }
    }

    function _resolve_path($path)
    {
        $parts = explode('/', $path);
        $absolutes = array();

        foreach( $parts as $part )
        {
            switch($part)
            {
                case '.':
                    break;

                case '..':
                    array_pop($absolutes);
                    break;

                default:
                    $absolutes[] = $part;
                    break;
            }
        }

        return preg_replace('~/+~', '/', implode('/', $absolutes));
    }
}

?>
