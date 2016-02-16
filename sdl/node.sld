<?xml version="1.0" encoding="UTF-8"?><sld:StyledLayerDescriptor xmlns="http://www.opengis.net/sld" xmlns:sld="http://www.opengis.net/sld" xmlns:ogc="http://www.opengis.net/ogc" xmlns:gml="http://www.opengis.net/gml" version="1.0.0">
  <sld:NamedLayer>
    <sld:Name>node</sld:Name>
    <sld:UserStyle>
      <sld:Name>node</sld:Name>
      <sld:Title>Default point</sld:Title>
      <sld:IsDefault>1</sld:IsDefault>
      <sld:FeatureTypeStyle>
        <sld:Name>name</sld:Name>
        <sld:Rule>
          <sld:Title>Скелет</sld:Title>
          <sld:PointSymbolizer>
            <sld:Graphic>
              <sld:Mark>
                <sld:WellKnownName>circle</sld:WellKnownName>
                <sld:Stroke>
                  <sld:CssParameter name="stroke-opacity">0.5</sld:CssParameter>
                </sld:Stroke>
              </sld:Mark>
              <sld:Size>0.5</sld:Size>
            </sld:Graphic>
          </sld:PointSymbolizer>
        </sld:Rule>
        <sld:Rule>
          <sld:Title>2132 узел</sld:Title>
          <ogc:Filter>
            <ogc:Or>
              <ogc:PropertyIsEqualTo>
                <ogc:PropertyName>type</ogc:PropertyName>
                <ogc:Literal>0</ogc:Literal>
              </ogc:PropertyIsEqualTo>
              <ogc:PropertyIsEqualTo>
                <ogc:PropertyName>type</ogc:PropertyName>
                <ogc:Literal>2</ogc:Literal>
              </ogc:PropertyIsEqualTo>
            </ogc:Or>
          </ogc:Filter>
          <sld:MinScaleDenominator>2131.0</sld:MinScaleDenominator>
          <sld:MaxScaleDenominator>2133.0</sld:MaxScaleDenominator>
          <sld:PointSymbolizer>
            <sld:Graphic>
              <sld:Mark>
                <sld:Fill>
                  <sld:CssParameter name="fill">#EEEEEE</sld:CssParameter>
                  <sld:CssParameter name="fill-opacity">0.6</sld:CssParameter>
                </sld:Fill>
                <sld:Stroke>
                  <sld:CssParameter name="stroke-width">2</sld:CssParameter>
                </sld:Stroke>
              </sld:Mark>
              <sld:Size>32</sld:Size>
            </sld:Graphic>
          </sld:PointSymbolizer>
          <sld:TextSymbolizer>
            <sld:Label>
              <ogc:PropertyName>loc_text</ogc:PropertyName>
            </sld:Label>
            <sld:Font>
              <sld:CssParameter name="font-family">Arial</sld:CssParameter>
              <sld:CssParameter name="font-size">14</sld:CssParameter>
              <sld:CssParameter name="font-style">normal</sld:CssParameter>
              <sld:CssParameter name="font-weight">normal</sld:CssParameter>
            </sld:Font>
            <sld:LabelPlacement>
              <sld:PointPlacement>
                <sld:AnchorPoint>
                  <sld:AnchorPointX>0.5</sld:AnchorPointX>
                  <sld:AnchorPointY>0.5</sld:AnchorPointY>
                </sld:AnchorPoint>
              </sld:PointPlacement>
            </sld:LabelPlacement>
            <sld:Halo>
              <sld:Radius>2</sld:Radius>
              <sld:Fill>
                <sld:CssParameter name="fill">#FFFFFF</sld:CssParameter>
                <sld:CssParameter name="fill-opacity">0.55</sld:CssParameter>
              </sld:Fill>
            </sld:Halo>
            <sld:Priority>
              <ogc:PropertyName>type</ogc:PropertyName>
            </sld:Priority>
            <sld:VendorOption name="followLine">true</sld:VendorOption>
            <sld:VendorOption name="autoWrap">50</sld:VendorOption>
            <sld:VendorOption name="repeat">1</sld:VendorOption>
            <sld:VendorOption name="forceLeftToRight">true</sld:VendorOption>
            <sld:VendorOption name="maxDisplacement">200</sld:VendorOption>
          </sld:TextSymbolizer>
        </sld:Rule>
        <sld:Rule>
          <sld:Title>4265 узел</sld:Title>
          <ogc:Filter>
            <ogc:Or>
              <ogc:PropertyIsEqualTo>
                <ogc:PropertyName>type</ogc:PropertyName>
                <ogc:Literal>0</ogc:Literal>
              </ogc:PropertyIsEqualTo>
              <ogc:PropertyIsEqualTo>
                <ogc:PropertyName>type</ogc:PropertyName>
                <ogc:Literal>2</ogc:Literal>
              </ogc:PropertyIsEqualTo>
            </ogc:Or>
          </ogc:Filter>
          <sld:MinScaleDenominator>4264.0</sld:MinScaleDenominator>
          <sld:MaxScaleDenominator>4266.0</sld:MaxScaleDenominator>
          <sld:PointSymbolizer>
            <sld:Graphic>
              <sld:Mark>
                <sld:Fill>
                  <sld:CssParameter name="fill">#EEEEEE</sld:CssParameter>
                </sld:Fill>
                <sld:Stroke/>
              </sld:Mark>
              <sld:Size>18</sld:Size>
            </sld:Graphic>
          </sld:PointSymbolizer>
          <sld:TextSymbolizer>
            <sld:Label>
              <ogc:PropertyName>loc_text</ogc:PropertyName>
            </sld:Label>
            <sld:Font>
              <sld:CssParameter name="font-family">Arial</sld:CssParameter>
              <sld:CssParameter name="font-size">11</sld:CssParameter>
              <sld:CssParameter name="font-style">normal</sld:CssParameter>
              <sld:CssParameter name="font-weight">normal</sld:CssParameter>
            </sld:Font>
            <sld:LabelPlacement>
              <sld:PointPlacement>
                <sld:AnchorPoint>
                  <sld:AnchorPointX>0.5</sld:AnchorPointX>
                  <sld:AnchorPointY>0.5</sld:AnchorPointY>
                </sld:AnchorPoint>
              </sld:PointPlacement>
            </sld:LabelPlacement>
            <sld:Halo>
              <sld:Radius>2</sld:Radius>
              <sld:Fill>
                <sld:CssParameter name="fill">#FFFFFF</sld:CssParameter>
                <sld:CssParameter name="fill-opacity">0.55</sld:CssParameter>
              </sld:Fill>
            </sld:Halo>
            <sld:VendorOption name="autoWrap">50</sld:VendorOption>
            <sld:VendorOption name="maxDisplacement">200</sld:VendorOption>
          </sld:TextSymbolizer>
        </sld:Rule>
        <sld:Rule>
          <sld:Title>8530 узел</sld:Title>
          <ogc:Filter>
            <ogc:Or>
              <ogc:PropertyIsEqualTo>
                <ogc:PropertyName>type</ogc:PropertyName>
                <ogc:Literal>0</ogc:Literal>
              </ogc:PropertyIsEqualTo>
              <ogc:PropertyIsEqualTo>
                <ogc:PropertyName>type</ogc:PropertyName>
                <ogc:Literal>2</ogc:Literal>
              </ogc:PropertyIsEqualTo>
            </ogc:Or>
          </ogc:Filter>
          <sld:MinScaleDenominator>8529.0</sld:MinScaleDenominator>
          <sld:MaxScaleDenominator>8531.0</sld:MaxScaleDenominator>
          <sld:PointSymbolizer>
            <sld:Graphic>
              <sld:Mark>
                <sld:Fill>
                  <sld:CssParameter name="fill">#EEEEEE</sld:CssParameter>
                </sld:Fill>
                <sld:Stroke/>
              </sld:Mark>
              <sld:Size>10</sld:Size>
            </sld:Graphic>
          </sld:PointSymbolizer>
          <sld:TextSymbolizer>
            <sld:Label>
              <ogc:PropertyName>loc_text</ogc:PropertyName>
            </sld:Label>
            <sld:Font>
              <sld:CssParameter name="font-family">Arial</sld:CssParameter>
              <sld:CssParameter name="font-size">9</sld:CssParameter>
              <sld:CssParameter name="font-style">normal</sld:CssParameter>
              <sld:CssParameter name="font-weight">normal</sld:CssParameter>
            </sld:Font>
            <sld:LabelPlacement>
              <sld:PointPlacement>
                <sld:AnchorPoint>
                  <sld:AnchorPointX>0.5</sld:AnchorPointX>
                  <sld:AnchorPointY>0.5</sld:AnchorPointY>
                </sld:AnchorPoint>
              </sld:PointPlacement>
            </sld:LabelPlacement>
            <sld:Halo>
              <sld:Radius>1.0</sld:Radius>
              <sld:Fill>
                <sld:CssParameter name="fill">#FFFFFF</sld:CssParameter>
                <sld:CssParameter name="fill-opacity">0.55</sld:CssParameter>
              </sld:Fill>
            </sld:Halo>
            <sld:VendorOption name="conflictResolution">true</sld:VendorOption>
            <sld:VendorOption name="autoWrap">50</sld:VendorOption>
            <sld:VendorOption name="group">no</sld:VendorOption>
            <sld:VendorOption name="maxDisplacement">200</sld:VendorOption>
          </sld:TextSymbolizer>
        </sld:Rule>
        <sld:Rule>
          <sld:Title>17061 узел</sld:Title>
          <ogc:Filter>
            <ogc:Or>
              <ogc:PropertyIsEqualTo>
                <ogc:PropertyName>type</ogc:PropertyName>
                <ogc:Literal>0</ogc:Literal>
              </ogc:PropertyIsEqualTo>
              <ogc:PropertyIsEqualTo>
                <ogc:PropertyName>type</ogc:PropertyName>
                <ogc:Literal>2</ogc:Literal>
              </ogc:PropertyIsEqualTo>
            </ogc:Or>
          </ogc:Filter>
          <sld:MinScaleDenominator>17060.0</sld:MinScaleDenominator>
          <sld:MaxScaleDenominator>17062.0</sld:MaxScaleDenominator>
          <sld:PointSymbolizer>
            <sld:Graphic>
              <sld:Mark>
                <sld:Fill>
                  <sld:CssParameter name="fill">#EEEEEE</sld:CssParameter>
                </sld:Fill>
                <sld:Stroke/>
              </sld:Mark>
              <sld:Size>3</sld:Size>
            </sld:Graphic>
          </sld:PointSymbolizer>
        </sld:Rule>
        <sld:Rule>
          <sld:Title>2132 муфта</sld:Title>
          <ogc:Filter>
            <ogc:PropertyIsEqualTo>
              <ogc:PropertyName>type</ogc:PropertyName>
              <ogc:Literal>1</ogc:Literal>
            </ogc:PropertyIsEqualTo>
          </ogc:Filter>
          <sld:MinScaleDenominator>2131.0</sld:MinScaleDenominator>
          <sld:MaxScaleDenominator>2133.0</sld:MaxScaleDenominator>
          <sld:PointSymbolizer>
            <sld:Graphic>
              <sld:Mark>
                <sld:WellKnownName>triangle</sld:WellKnownName>
                <sld:Fill>
                  <sld:CssParameter name="fill">#EEEEEE</sld:CssParameter>
                  <sld:CssParameter name="fill-opacity">0.5</sld:CssParameter>
                </sld:Fill>
                <sld:Stroke>
                  <sld:CssParameter name="stroke-width">2</sld:CssParameter>
                </sld:Stroke>
              </sld:Mark>
              <sld:Size>32</sld:Size>
            </sld:Graphic>
          </sld:PointSymbolizer>
          <sld:TextSymbolizer>
            <sld:Label>
              <ogc:PropertyName>loc_text</ogc:PropertyName>
            </sld:Label>
            <sld:Font>
              <sld:CssParameter name="font-family">Arial</sld:CssParameter>
              <sld:CssParameter name="font-size">14</sld:CssParameter>
              <sld:CssParameter name="font-style">normal</sld:CssParameter>
              <sld:CssParameter name="font-weight">normal</sld:CssParameter>
            </sld:Font>
            <sld:LabelPlacement>
              <sld:PointPlacement>
                <sld:AnchorPoint>
                  <sld:AnchorPointX>0.5</sld:AnchorPointX>
                  <sld:AnchorPointY>1</sld:AnchorPointY>
                </sld:AnchorPoint>
              </sld:PointPlacement>
            </sld:LabelPlacement>
            <sld:Halo>
              <sld:Radius>2</sld:Radius>
              <sld:Fill>
                <sld:CssParameter name="fill">#FFFFFF</sld:CssParameter>
                <sld:CssParameter name="fill-opacity">0.55</sld:CssParameter>
              </sld:Fill>
            </sld:Halo>
            <sld:VendorOption name="followLine">true</sld:VendorOption>
            <sld:VendorOption name="autoWrap">50</sld:VendorOption>
            <sld:VendorOption name="maxDisplacement">200</sld:VendorOption>
            <sld:VendorOption name="forceLeftToRight">true</sld:VendorOption>
          </sld:TextSymbolizer>
        </sld:Rule>
        <sld:Rule>
          <sld:Title>4265 муфта</sld:Title>
          <ogc:Filter>
            <ogc:PropertyIsEqualTo>
              <ogc:PropertyName>type</ogc:PropertyName>
              <ogc:Literal>1</ogc:Literal>
            </ogc:PropertyIsEqualTo>
          </ogc:Filter>
          <sld:MinScaleDenominator>4264.0</sld:MinScaleDenominator>
          <sld:MaxScaleDenominator>4266.0</sld:MaxScaleDenominator>
          <sld:PointSymbolizer>
            <sld:Graphic>
              <sld:Mark>
                <sld:WellKnownName>triangle</sld:WellKnownName>
                <sld:Fill>
                  <sld:CssParameter name="fill">#EEEEEE</sld:CssParameter>
                  <sld:CssParameter name="fill-opacity">0.5</sld:CssParameter>
                </sld:Fill>
                <sld:Stroke/>
              </sld:Mark>
              <sld:Size>18</sld:Size>
            </sld:Graphic>
          </sld:PointSymbolizer>
          <sld:TextSymbolizer>
            <sld:Label>
              <ogc:PropertyName>loc_text</ogc:PropertyName>
            </sld:Label>
            <sld:Font>
              <sld:CssParameter name="font-family">Arial</sld:CssParameter>
              <sld:CssParameter name="font-size">11</sld:CssParameter>
              <sld:CssParameter name="font-style">normal</sld:CssParameter>
              <sld:CssParameter name="font-weight">normal</sld:CssParameter>
            </sld:Font>
            <sld:LabelPlacement>
              <sld:PointPlacement>
                <sld:AnchorPoint>
                  <sld:AnchorPointX>0.5</sld:AnchorPointX>
                  <sld:AnchorPointY>0.5</sld:AnchorPointY>
                </sld:AnchorPoint>
              </sld:PointPlacement>
            </sld:LabelPlacement>
            <sld:Halo>
              <sld:Radius>2</sld:Radius>
              <sld:Fill>
                <sld:CssParameter name="fill">#FFFFFF</sld:CssParameter>
                <sld:CssParameter name="fill-opacity">0.55</sld:CssParameter>
              </sld:Fill>
            </sld:Halo>
            <sld:VendorOption name="autoWrap">50</sld:VendorOption>
            <sld:VendorOption name="maxDisplacement">200</sld:VendorOption>
          </sld:TextSymbolizer>
        </sld:Rule>
        <sld:Rule>
          <sld:Title>8530 муфта</sld:Title>
          <ogc:Filter>
            <ogc:PropertyIsEqualTo>
              <ogc:PropertyName>type</ogc:PropertyName>
              <ogc:Literal>1</ogc:Literal>
            </ogc:PropertyIsEqualTo>
          </ogc:Filter>
          <sld:MinScaleDenominator>8529.0</sld:MinScaleDenominator>
          <sld:MaxScaleDenominator>8531.0</sld:MaxScaleDenominator>
          <sld:PointSymbolizer>
            <sld:Graphic>
              <sld:Mark>
                <sld:WellKnownName>triangle</sld:WellKnownName>
                <sld:Fill>
                  <sld:CssParameter name="fill">#EEEEEE</sld:CssParameter>
                  <sld:CssParameter name="fill-opacity">0.5</sld:CssParameter>
                </sld:Fill>
                <sld:Stroke/>
              </sld:Mark>
              <sld:Size>10</sld:Size>
            </sld:Graphic>
          </sld:PointSymbolizer>
          <sld:TextSymbolizer>
            <sld:Label>
              <ogc:PropertyName>loc_text</ogc:PropertyName>
            </sld:Label>
            <sld:Font>
              <sld:CssParameter name="font-family">Arial</sld:CssParameter>
              <sld:CssParameter name="font-size">9</sld:CssParameter>
              <sld:CssParameter name="font-style">normal</sld:CssParameter>
              <sld:CssParameter name="font-weight">normal</sld:CssParameter>
            </sld:Font>
            <sld:LabelPlacement>
              <sld:PointPlacement>
                <sld:AnchorPoint>
                  <sld:AnchorPointX>0.5</sld:AnchorPointX>
                  <sld:AnchorPointY>0.5</sld:AnchorPointY>
                </sld:AnchorPoint>
              </sld:PointPlacement>
            </sld:LabelPlacement>
            <sld:Halo>
              <sld:Radius>1.0</sld:Radius>
              <sld:Fill>
                <sld:CssParameter name="fill">#FFFFFF</sld:CssParameter>
                <sld:CssParameter name="fill-opacity">0.55</sld:CssParameter>
              </sld:Fill>
            </sld:Halo>
            <sld:VendorOption name="autoWrap">50</sld:VendorOption>
            <sld:VendorOption name="maxDisplacement">200</sld:VendorOption>
          </sld:TextSymbolizer>
        </sld:Rule>
        <sld:Rule>
          <sld:Title>17061 муфта</sld:Title>
          <ogc:Filter>
            <ogc:PropertyIsEqualTo>
              <ogc:PropertyName>type</ogc:PropertyName>
              <ogc:Literal>1</ogc:Literal>
            </ogc:PropertyIsEqualTo>
          </ogc:Filter>
          <sld:MinScaleDenominator>17060.0</sld:MinScaleDenominator>
          <sld:MaxScaleDenominator>17062.0</sld:MaxScaleDenominator>
          <sld:PointSymbolizer>
            <sld:Graphic>
              <sld:Mark>
                <sld:WellKnownName>triangle</sld:WellKnownName>
                <sld:Fill>
                  <sld:CssParameter name="fill">#EEEEEE</sld:CssParameter>
                </sld:Fill>
                <sld:Stroke/>
              </sld:Mark>
              <sld:Size>3</sld:Size>
            </sld:Graphic>
          </sld:PointSymbolizer>
        </sld:Rule>
        <sld:Rule>
          <sld:Title>2132 новый узел</sld:Title>
          <ogc:Filter>
            <ogc:PropertyIsNotEqualTo>
              <ogc:PropertyName>is_new</ogc:PropertyName>
              <ogc:Literal>false</ogc:Literal>
            </ogc:PropertyIsNotEqualTo>
          </ogc:Filter>
          <sld:MinScaleDenominator>2131.0</sld:MinScaleDenominator>
          <sld:MaxScaleDenominator>2133.0</sld:MaxScaleDenominator>
          <sld:PointSymbolizer>
            <sld:Graphic>
              <sld:Mark>
                <sld:Fill>
                  <sld:CssParameter name="fill">#FF0000</sld:CssParameter>
                </sld:Fill>
                <sld:Stroke>
                  <sld:CssParameter name="stroke-width">2</sld:CssParameter>
                </sld:Stroke>
              </sld:Mark>
              <sld:Size>32</sld:Size>
            </sld:Graphic>
          </sld:PointSymbolizer>
        </sld:Rule>
        <sld:Rule>
          <sld:Title>4265 новый узел</sld:Title>
          <ogc:Filter>
            <ogc:PropertyIsNotEqualTo>
              <ogc:PropertyName>is_new</ogc:PropertyName>
              <ogc:Literal>false</ogc:Literal>
            </ogc:PropertyIsNotEqualTo>
          </ogc:Filter>
          <sld:MinScaleDenominator>4264.0</sld:MinScaleDenominator>
          <sld:MaxScaleDenominator>4266.0</sld:MaxScaleDenominator>
          <sld:PointSymbolizer>
            <sld:Graphic>
              <sld:Mark>
                <sld:Fill>
                  <sld:CssParameter name="fill">#FF0000</sld:CssParameter>
                </sld:Fill>
                <sld:Stroke/>
              </sld:Mark>
              <sld:Size>18</sld:Size>
            </sld:Graphic>
          </sld:PointSymbolizer>
        </sld:Rule>
        <sld:Rule>
          <sld:Title>8530 новый узел</sld:Title>
          <ogc:Filter>
            <ogc:PropertyIsNotEqualTo>
              <ogc:PropertyName>is_new</ogc:PropertyName>
              <ogc:Literal>false</ogc:Literal>
            </ogc:PropertyIsNotEqualTo>
          </ogc:Filter>
          <sld:MinScaleDenominator>8529.0</sld:MinScaleDenominator>
          <sld:MaxScaleDenominator>8531.0</sld:MaxScaleDenominator>
          <sld:PointSymbolizer>
            <sld:Graphic>
              <sld:Mark>
                <sld:Fill>
                  <sld:CssParameter name="fill">#FF0000</sld:CssParameter>
                </sld:Fill>
                <sld:Stroke/>
              </sld:Mark>
              <sld:Size>10</sld:Size>
            </sld:Graphic>
          </sld:PointSymbolizer>
        </sld:Rule>
        <sld:Rule>
          <sld:Title>17061 новый узел</sld:Title>
          <ogc:Filter>
            <ogc:PropertyIsNotEqualTo>
              <ogc:PropertyName>is_new</ogc:PropertyName>
              <ogc:Literal>false</ogc:Literal>
            </ogc:PropertyIsNotEqualTo>
          </ogc:Filter>
          <sld:MinScaleDenominator>17060.0</sld:MinScaleDenominator>
          <sld:MaxScaleDenominator>17062.0</sld:MaxScaleDenominator>
          <sld:PointSymbolizer>
            <sld:Graphic>
              <sld:Mark>
                <sld:Fill>
                  <sld:CssParameter name="fill">#FF0000</sld:CssParameter>
                </sld:Fill>
                <sld:Stroke>
                  <sld:CssParameter name="stroke">#FF0000</sld:CssParameter>
                </sld:Stroke>
              </sld:Mark>
              <sld:Size>3</sld:Size>
            </sld:Graphic>
          </sld:PointSymbolizer>
        </sld:Rule>
        <sld:Rule>
          <sld:Title>2132 проблема</sld:Title>
          <ogc:Filter>
            <ogc:PropertyIsEqualTo>
              <ogc:PropertyName>incorrect</ogc:PropertyName>
              <ogc:Literal>true</ogc:Literal>
            </ogc:PropertyIsEqualTo>
          </ogc:Filter>
          <sld:MinScaleDenominator>2131.0</sld:MinScaleDenominator>
          <sld:MaxScaleDenominator>2133.0</sld:MaxScaleDenominator>
          <sld:PointSymbolizer>
            <sld:Graphic>
              <sld:Mark>
                <sld:WellKnownName>x</sld:WellKnownName>
                <sld:Fill>
                  <sld:CssParameter name="fill">#FF0000</sld:CssParameter>
                </sld:Fill>
              </sld:Mark>
              <sld:Size>34</sld:Size>
            </sld:Graphic>
          </sld:PointSymbolizer>
        </sld:Rule>
        <sld:Rule>
          <sld:Title>4265 проблема</sld:Title>
          <ogc:Filter>
            <ogc:PropertyIsEqualTo>
              <ogc:PropertyName>incorrect</ogc:PropertyName>
              <ogc:Literal>1</ogc:Literal>
            </ogc:PropertyIsEqualTo>
          </ogc:Filter>
          <sld:MinScaleDenominator>4264.0</sld:MinScaleDenominator>
          <sld:MaxScaleDenominator>4266.0</sld:MaxScaleDenominator>
          <sld:PointSymbolizer>
            <sld:Graphic>
              <sld:Mark>
                <sld:WellKnownName>x</sld:WellKnownName>
                <sld:Fill>
                  <sld:CssParameter name="fill">#FF0000</sld:CssParameter>
                </sld:Fill>
              </sld:Mark>
              <sld:Size>18</sld:Size>
            </sld:Graphic>
          </sld:PointSymbolizer>
        </sld:Rule>
        <sld:Rule>
          <sld:Title>8530 проблема</sld:Title>
          <ogc:Filter>
            <ogc:PropertyIsEqualTo>
              <ogc:PropertyName>incorrect</ogc:PropertyName>
              <ogc:Literal>1</ogc:Literal>
            </ogc:PropertyIsEqualTo>
          </ogc:Filter>
          <sld:MinScaleDenominator>8529.0</sld:MinScaleDenominator>
          <sld:MaxScaleDenominator>8531.0</sld:MaxScaleDenominator>
          <sld:PointSymbolizer>
            <sld:Graphic>
              <sld:Mark>
                <sld:WellKnownName>x</sld:WellKnownName>
                <sld:Fill>
                  <sld:CssParameter name="fill">#FF0000</sld:CssParameter>
                </sld:Fill>
              </sld:Mark>
              <sld:Size>9</sld:Size>
            </sld:Graphic>
          </sld:PointSymbolizer>
        </sld:Rule>
        <sld:Rule>
          <sld:Title>17061 проблема</sld:Title>
          <ogc:Filter>
            <ogc:PropertyIsEqualTo>
              <ogc:PropertyName>incorrect</ogc:PropertyName>
              <ogc:Literal>1</ogc:Literal>
            </ogc:PropertyIsEqualTo>
          </ogc:Filter>
          <sld:MinScaleDenominator>17060.0</sld:MinScaleDenominator>
          <sld:MaxScaleDenominator>17062.0</sld:MaxScaleDenominator>
          <sld:PointSymbolizer>
            <sld:Graphic>
              <sld:Mark>
                <sld:WellKnownName>x</sld:WellKnownName>
                <sld:Fill>
                  <sld:CssParameter name="fill">#FF0000</sld:CssParameter>
                </sld:Fill>
              </sld:Mark>
              <sld:Size>8</sld:Size>
            </sld:Graphic>
          </sld:PointSymbolizer>
        </sld:Rule>
      </sld:FeatureTypeStyle>
    </sld:UserStyle>
  </sld:NamedLayer>
</sld:StyledLayerDescriptor>
