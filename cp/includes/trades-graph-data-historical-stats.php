<vc:Chart xmlns:vc="clr-namespace:Visifire.Charts;assembly=SLVisifire.Charts" Width="850" Height="400" BorderThickness="0" Theme="Theme1" AnimationEnabled="False" Watermark="False"> <!-- DataPointWidth="5.0"> -->
    <vc:Chart.Titles>
        <vc:Title Text="Historical In, Out, &amp; Clicks" FontWeight="Bold" />
    </vc:Chart.Titles>

    <vc:Chart.ToolTips>
        <vc:ToolTip Opacity="1.0" Background="Black" FontColor="White" FontWeight="Bold" FontSize="10.0"/>
    </vc:Chart.ToolTips>

    <vc:Chart.AxesX>
        <vc:Axis><!-- ScrollBarScale="0.55"> -->
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
        <vc:DataSeries Color="#f6bd11" Opacity="0.85" LegendText="In" RenderAs="Column" AxisYType="Primary" Bevel="False" LightingEnabled="False" LabelEnabled="True" LabelStyle="Inside" LabelFontColor="Black" LabelFontSize="9.0" ToolTipText="{x:Null}">
            <vc:DataSeries.DataPoints>
                <?php foreach( $history->stats as $date => $stats ): ?>
                <vc:DataPoint AxisXLabel="<?php echo $date; ?>" YValue="<?php echo $stats[0]; ?>"/>
                <?php endforeach; ?>
            </vc:DataSeries.DataPoints>
        </vc:DataSeries>

        <vc:DataSeries Color="#b1d9f8" Opacity="0.85" LegendText="Out" RenderAs="Column" AxisYType="Primary" Bevel="False" LightingEnabled="False" LabelEnabled="True" LabelStyle="Inside" LabelFontColor="Black" LabelFontSize="9.0" ToolTipText="{x:Null}">
            <vc:DataSeries.DataPoints>
                <?php foreach( $history->stats as $date => $stats ): ?>
                <vc:DataPoint AxisXLabel="<?php echo $date; ?>" YValue="<?php echo $stats[14]; ?>"/>
                <?php endforeach; ?>
            </vc:DataSeries.DataPoints>
        </vc:DataSeries>

        <vc:DataSeries Color="#8dbb05" Opacity="0.85" LegendText="Clicks" RenderAs="Column" AxisYType="Primary" Bevel="False" LightingEnabled="False" LabelEnabled="True" LabelStyle="Inside" LabelFontColor="Black" LabelFontSize="9.0" ToolTipText="{x:Null}">
            <vc:DataSeries.DataPoints>
                <?php foreach( $history->stats as $date => $stats ): ?>
                <vc:DataPoint AxisXLabel="<?php echo $date; ?>" YValue="<?php echo $stats[6]; ?>"/>
                <?php endforeach; ?>
            </vc:DataSeries.DataPoints>
        </vc:DataSeries>
    </vc:Chart.Series>

</vc:Chart>
