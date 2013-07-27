

if (!window.Visifire) {

    window.Visifire = function(pXapPath, pId, pWidth, pHeight, pBackground, pWindowless) {
        this.id = null;                             // Silverlight object id.
        this.logLevel = 1;                          // Determines whether to log or not.
        this.xapPath = "SL.Visifire.Charts.xap";    // xap file path (default is taken as Visifire.xap in the same directory).
        this.targetElement = null;                  // Target div element name.
        this.dataXml = null;                        // Chart Xml string.
        this.dataUri = null;                        // Chart xml file uri path.
        this.windowless = null;                     // Windowless property.
        this.width = null;                          // Width of the chart.
        this.height = null;                         // Height of the chart container.
        this.background = null;                     // Background of the chart container.
        this.preLoad = null;                        // Preload event handler.
        this.loaded = null;                         // Loaded event handler.
        this.onError = null;                        // OnError event handler.

        this.charts = null;

        //  pId not present
        if (Number(pId)) {
            if (pHeight)
                this.background = pHeight;

            pHeight = pWidth;
            pWidth = pId;
        }
        else    // pId present
        {
            this.id = pId;

            if (pBackground)
                this.background = pBackground;
        }

        if (pXapPath)
            this.xapPath = pXapPath;

        if (pWidth)
            this.width = pWidth;

        if (pHeight)
            this.height = pHeight;

        if (pBackground)
            this.background = pBackground;

        if (pWindowless)
            this.windowless = pWindowless;

        this.vThisObject = this;

        this.index = ++Visifire._slCount;
    }

    window.Visifire._slCount = 0;  // Number of Visifire controls present in the current window.

    Visifire.prototype.setWindowlessState = function(pWindowless) {
        if (pWindowless != null)
            this.windowless = Boolean(pWindowless);
    }

    Visifire.prototype.setDataXml = function(pDataXml) {
        var slControl = this._getSlControl();

        this.dataXml = pDataXml;
        
        if (slControl != null && this.dataXml != null)
            slControl.Content.wrapper.AddDataXML(pDataXml);
    }

    Visifire.prototype.setDataUri = function(pDataUri) {
        var slControl = this._getSlControl();

        this.dataUri = pDataUri;

        if (slControl != null && this.dataUri != null)
            slControl.Content.wrapper.AddDataUri(pDataUri);
    }

    Visifire.prototype.render = function(pTargetElement) {
        var vThisObject = this;            // This Class
        var vSlControl = this._getSlControl();

        vThisObject._attachEvents();

        if (vSlControl == null)
            this._render(pTargetElement);
        else
            this._reRender(vSlControl);
    }

    Visifire.prototype.setSize = function(pWidth, pHeight) {
        var slControl = this._getSlControl();

        if (slControl != null) {
            slControl.width = pWidth;
            slControl.height = pHeight;
            slControl.Content.wrapper.Resize(pWidth, pHeight);
        }
        else {
            this.width = pWidth;
            this.height = pHeight;
        }
    }

    Visifire.prototype.setLogLevel = function(pLevel) {
        if (pLevel != null)
            this.logLevel = pLevel;
    }

    Visifire.prototype.isLoaded = function() {
        var slControl = this._getSlControl();

        try {
            if (slControl.Content.wrapper != null)
                return true;
        }
        catch (ex) {
            return false;
        }
    }

    Visifire.prototype.isDataLoaded = function() {
        var slControl = this._getSlControl();
        return slControl.Content.wrapper.IsDataLoaded;
    }

    Visifire.prototype._attachEvents = function() {
        var vThisObject = this; // This Class

        window["setVisifireChartsRef" + vThisObject.index] = function(e) {
            vThisObject.charts = e;
        }

        if (vThisObject.preLoad != null)
            window["visifireChartPreLoad" + vThisObject.index] = vThisObject.preLoad;

        if (vThisObject.loaded != null)
            window["visifireChartLoaded" + vThisObject.index] = vThisObject.loaded;

        if (vThisObject.onError != null)
            window["visifireChartOnError" + vThisObject.index] = vThisObject.onError;
    }

    Visifire.prototype._getSlControl = function() {
        var vThisObject = this; // This Class
        
        if (vThisObject.id != null) {
            var slControl = document.getElementById(vThisObject.id);
            return slControl;
        }

        return null;
    }

    Visifire.prototype._render = function(pTargetElement) {
        var vThisObject = this;            // This Class
        var vWidth;                        // Width of the chart container
        var vHeight;                       // Height of the chart container

        vThisObject.targetElement = (typeof (pTargetElement) == "string") ? document.getElementById(pTargetElement) : pTargetElement;

        vWidth = (vThisObject.width != null) ? vThisObject.width : (vThisObject.targetElement.offsetWidth != 0) ? vThisObject.targetElement.offsetWidth : 500;

        vHeight = (vThisObject.height != null) ? vThisObject.height : (vThisObject.targetElement.offsetHeight != 0) ? vThisObject.targetElement.offsetHeight : 300;

        if (!vThisObject.id)
            vThisObject.id = 'VisifireControl' + vThisObject.index;

        var html = '<object id="' + vThisObject.id + '" data="data:application/x-silverlight," type="application/x-silverlight-2" width="' + vWidth + '" height="' + vHeight + '">';

        html += '<param name="source" value="' + vThisObject.xapPath + '"/>'
        html += '<param name="initParams" value="';
        html += "logLevel=" + vThisObject.logLevel + ",";
        html += "controlId=" + vThisObject.id + ",";
        html += "setVisifireChartsRef=setVisifireChartsRef" + vThisObject.index + ",";

        if (vThisObject.preLoad != null)
            html += "onChartPreLoad=visifireChartPreLoad" + vThisObject.index + ",";

        if (vThisObject.loaded != null)
            html += "onChartLoaded=visifireChartLoaded" + vThisObject.index + ",";

        if (vThisObject.dataXml != null) {
            window["getVisifireDataXml" + vThisObject.index] = function(sender, args) {
                var _uThisObj = vThisObject;
                return _uThisObj.dataXml;
            };

            html += 'dataXml=getVisifireDataXml' + vThisObject.index + ',';
        }
        else if (vThisObject.dataUri != null) {
            html += 'dataUri=' + vThisObject.dataUri + ',';
        }
            
        if (vThisObject.background == null)
            vThisObject.background = "White";

        if (vThisObject.windowless == null) {
            if (vThisObject.background == "Transparent" || vThisObject.background == "transparent")
                vThisObject.windowless = true;
            else
                vThisObject.windowless = false;
        }

        html += 'width=' + vWidth + ',' + 'height=' + vHeight + '';
        html += "\"/>";
        
        if (vThisObject.onError != null)
            html += '<param name="onError" value="visifireChartOnError' + vThisObject.index + '" />'
        
        html += '<param name="enableHtmlAccess" value="true" />'
		        + '<param name="background" value="' + vThisObject.background + '" />'
		        + '<param name="windowless" value="' + vThisObject.windowless + '" />'
		        + '<a href="http://go.microsoft.com/fwlink/?LinkID=149156&v=3.0.40624.0" style="text-decoration: none;">'
		        + '<img src="http://go.microsoft.com/fwlink/?LinkId=108181" alt="Get Microsoft Silverlight" style="border-style: none"/>'
		        + '<br/>You need Microsoft Silverlight to view Visifire Charts.'
		        + '<br/> You can install it by clicking on this link.'
		        + '<br/>Please restart the browser after installation.'
		        + '</a>'
		        + '</object>';

        this.targetElement.innerHTML = html;
    }

    Visifire.prototype._reRender = function(pSlControl) {
        pSlControl.Content.wrapper.ReRenderChart();
    }
}