<vc:Chart xmlns:vc="clr-namespace:Visifire.Charts;assembly=SLVisifire.Charts" Width="850" Height="400" BorderThickness="0" Theme="Theme1" AnimationEnabled="False" Watermark="False" DataPointWidth="5.0">
    <vc:Chart.Titles>
        <vc:Title Text="Historical Productivity &amp; Return" FontWeight="Bold" />
    </vc:Chart.Titles>

    <vc:Chart.ToolTips>
        <vc:ToolTip Opacity="1.0" Background="Black" FontColor="White" FontWeight="Bold" FontSize="10.0"/>
    </vc:Chart.ToolTips>

    <vc:Chart.AxesX>
        <vc:Axis>
            <vc:Axis.AxisLabels>
                <vc:AxisLabels Angle="-90"/>
            </vc:Axis.AxisLabels>
        </vc:Axis>
    </vc:Chart.AxesX>

    <vc:Chart.AxesY>
        <vc:Axis AxisType="Primary">
            <vc:Axis.Grids>
                <vc:ChartGrid InterlacedColor="#ececec"/>
            </vc:Axis.Grids>
        </vc:Axis>
    </vc:Chart.AxesY>

    <vc:Chart.Series>
        <vc:DataSeries LegendText="Productivity" RenderAs="Line" LightingEnabled="False" ToolTipText="#YValue%" Color="#ff0000" MarkerScale="1.5" LineThickness="3" LabelEnabled="False">
            <vc:DataSeries.DataPoints>
                <?php foreach( $history->stats as $date => $stats ): ?>
                <vc:DataPoint AxisXLabel="<?php echo $date; ?>" YValue="<?php echo $stats[0] > 0 ? format_float_to_percent($stats[6] / $stats[0]) : 0; ?>"/>
                <?php endforeach; ?>
            </vc:DataSeries.DataPoints>
        </vc:DataSeries>

        <vc:DataSeries LegendText="Return" RenderAs="Line" LightingEnabled="False" ToolTipText="#YValue%" Color="#0097FF" MarkerScale="1.5" LineThickness="3" LabelEnabled="False">
            <vc:DataSeries.DataPoints>
                <?php foreach( $history->stats as $date => $stats ): ?>
                <vc:DataPoint AxisXLabel="<?php echo $date; ?>" YValue="<?php echo $stats[0] > 0 ? format_float_to_percent($stats[14] / $stats[0]) : 0; ?>"/>
                <?php endforeach; ?>
            </vc:DataSeries.DataPoints>
        </vc:DataSeries>
    </vc:Chart.Series>

</vc:Chart>