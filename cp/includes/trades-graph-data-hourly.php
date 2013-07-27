<vc:Chart xmlns:vc="clr-namespace:Visifire.Charts;assembly=SLVisifire.Charts" Width="850" Height="400" BorderThickness="0" Theme="Theme1" AnimationEnabled="False" Watermark="False" DataPointWidth="2.0">
    <vc:Chart.Titles>
        <vc:Title Text="Last 24 Hours In, Out, &amp; Clicks" FontWeight="Bold" />
    </vc:Chart.Titles>

    <vc:Chart.ToolTips>
        <vc:ToolTip Opacity="1.0" Background="Black" FontColor="White" FontWeight="Bold" FontSize="10.0"/>
    </vc:Chart.ToolTips>

    <vc:Chart.AxesX>
        <vc:Axis ScrollBarScale="0.55">
            <vc:Axis.AxisLabels>
                <vc:AxisLabels Angle="-45"/>
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
        <vc:DataSeries Color="#f6bd11" Opacity="0.85" LegendText="In" RenderAs="Column" AxisYType="Primary" Bevel="False" LightingEnabled="False" LabelEnabled="True" LabelStyle="Inside" LabelFontColor="Black" LabelFontSize="9.0" ToolTipText="{x:Null}">
            <vc:DataSeries.DataPoints>
                <?php for( $i = 0; $i < HOURS_PER_DAY; $i++ ): ?>
                <vc:DataPoint AxisXLabel="<?php printf('%02d', $i); ?>:00" YValue="<?php echo $stats->i_raw[$i]; ?>"/>
                <?php endfor; ?>
            </vc:DataSeries.DataPoints>
        </vc:DataSeries>

        <vc:DataSeries Color="#b1d9f8" Opacity="0.85" LegendText="Out" RenderAs="Column" AxisYType="Primary" Bevel="False" LightingEnabled="False" LabelEnabled="True" LabelStyle="Inside" LabelFontColor="Black" LabelFontSize="9.0" ToolTipText="{x:Null}">
            <vc:DataSeries.DataPoints>
                <?php for( $i = 0; $i < HOURS_PER_DAY; $i++ ): ?>
                <vc:DataPoint YValue="<?php echo $stats->o_raw[$i]; ?>"/>
                <?php endfor; ?>
            </vc:DataSeries.DataPoints>
        </vc:DataSeries>

        <vc:DataSeries Color="#8dbb05" Opacity="0.85" LegendText="Clicks" RenderAs="Column" AxisYType="Primary" Bevel="False" LightingEnabled="False" LabelEnabled="True" LabelStyle="Inside" LabelFontColor="Black" LabelFontSize="9.0" ToolTipText="{x:Null}">
            <vc:DataSeries.DataPoints>
                <?php for( $i = 0; $i < HOURS_PER_DAY; $i++ ): ?>
                <vc:DataPoint YValue="<?php echo $stats->c_raw[$i]; ?>"/>
                <?php endfor; ?>
            </vc:DataSeries.DataPoints>
        </vc:DataSeries>
    </vc:Chart.Series>

</vc:Chart>