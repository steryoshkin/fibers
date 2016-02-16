<?xml version="1.0" encoding="UTF-8"?><sld:StyledLayerDescriptor xmlns="http://www.opengis.net/sld" xmlns:sld="http://www.opengis.net/sld" xmlns:ogc="http://www.opengis.net/ogc" xmlns:gml="http://www.opengis.net/gml" version="1.0.0">
  <sld:NamedLayer>
    <sld:Name>cable</sld:Name>
    <sld:UserStyle>
      <sld:Name>cable</sld:Name>
      <sld:Title>cable</sld:Title>
      <sld:IsDefault>1</sld:IsDefault>
      <sld:FeatureTypeStyle>
        <sld:Name>name</sld:Name>
        <sld:Rule>
          <sld:Title>Скелет</sld:Title>
          <sld:LineSymbolizer>
            <sld:Stroke>
              <sld:CssParameter name="stroke-opacity">0.5</sld:CssParameter>
              <sld:CssParameter name="stroke-width">0.5</sld:CssParameter>
            </sld:Stroke>
          </sld:LineSymbolizer>
        </sld:Rule>
        <sld:Rule>
          <sld:Title>2132 оптика</sld:Title>
          <sld:Abstract>A 1 pixel wide black line</sld:Abstract>
          <sld:MinScaleDenominator>2031.0</sld:MinScaleDenominator>
          <sld:MaxScaleDenominator>2133.0</sld:MaxScaleDenominator>
          <sld:LineSymbolizer>
            <sld:Stroke>
              <sld:CssParameter name="stroke">#0000FF</sld:CssParameter>
              <sld:CssParameter name="stroke-opacity">0.55</sld:CssParameter>
              <sld:CssParameter name="stroke-width">3</sld:CssParameter>
            </sld:Stroke>
          </sld:LineSymbolizer>
        </sld:Rule>
        <sld:Rule>
          <sld:Title>4265 оптика</sld:Title>
          <sld:Abstract>A 1 pixel wide black line</sld:Abstract>
          <sld:MinScaleDenominator>4264.0</sld:MinScaleDenominator>
          <sld:MaxScaleDenominator>4266.0</sld:MaxScaleDenominator>
          <sld:LineSymbolizer>
            <sld:Stroke>
              <sld:CssParameter name="stroke">#0000FF</sld:CssParameter>
              <sld:CssParameter name="stroke-opacity">0.55</sld:CssParameter>
              <sld:CssParameter name="stroke-width">2</sld:CssParameter>
            </sld:Stroke>
          </sld:LineSymbolizer>
        </sld:Rule>
        <sld:Rule>
          <sld:Title>8530 оптика</sld:Title>
          <sld:Abstract>A 1 pixel wide black line</sld:Abstract>
          <sld:MinScaleDenominator>8529.0</sld:MinScaleDenominator>
          <sld:MaxScaleDenominator>8531.0</sld:MaxScaleDenominator>
          <sld:LineSymbolizer>
            <sld:Stroke>
              <sld:CssParameter name="stroke">#0000FF</sld:CssParameter>
              <sld:CssParameter name="stroke-opacity">0.55</sld:CssParameter>
            </sld:Stroke>
          </sld:LineSymbolizer>
        </sld:Rule>
        <sld:Rule>
          <sld:Title>17061 оптика</sld:Title>
          <sld:Abstract>A 1 pixel wide black line</sld:Abstract>
          <sld:MinScaleDenominator>17060.0</sld:MinScaleDenominator>
          <sld:MaxScaleDenominator>68248.0</sld:MaxScaleDenominator>
          <sld:LineSymbolizer>
            <sld:Stroke>
              <sld:CssParameter name="stroke">#0000FF</sld:CssParameter>
              <sld:CssParameter name="stroke-opacity">0.55</sld:CssParameter>
              <sld:CssParameter name="stroke-width">0.5</sld:CssParameter>
            </sld:Stroke>
          </sld:LineSymbolizer>
        </sld:Rule>
        <sld:Rule>
          <sld:Title>2132 медь</sld:Title>
          <sld:Abstract>A 1 pixel wide black line</sld:Abstract>
          <ogc:Filter>
            <ogc:PropertyIsEqualTo>
              <ogc:PropertyName>cable_type</ogc:PropertyName>
              <ogc:Literal>59</ogc:Literal>
            </ogc:PropertyIsEqualTo>
          </ogc:Filter>
          <sld:MinScaleDenominator>2131.0</sld:MinScaleDenominator>
          <sld:MaxScaleDenominator>2133.0</sld:MaxScaleDenominator>
          <sld:LineSymbolizer>
            <sld:Stroke>
              <sld:CssParameter name="stroke">#FF0000</sld:CssParameter>
              <sld:CssParameter name="stroke-width">3</sld:CssParameter>
            </sld:Stroke>
          </sld:LineSymbolizer>
        </sld:Rule>
        <sld:Rule>
          <sld:Title>4265 медь</sld:Title>
          <sld:Abstract>A 1 pixel wide black line</sld:Abstract>
          <ogc:Filter>
            <ogc:PropertyIsEqualTo>
              <ogc:PropertyName>cable_type</ogc:PropertyName>
              <ogc:Literal>59</ogc:Literal>
            </ogc:PropertyIsEqualTo>
          </ogc:Filter>
          <sld:MinScaleDenominator>4264.0</sld:MinScaleDenominator>
          <sld:MaxScaleDenominator>4266.0</sld:MaxScaleDenominator>
          <sld:LineSymbolizer>
            <sld:Stroke>
              <sld:CssParameter name="stroke">#FF0000</sld:CssParameter>
              <sld:CssParameter name="stroke-width">2</sld:CssParameter>
            </sld:Stroke>
          </sld:LineSymbolizer>
        </sld:Rule>
        <sld:Rule>
          <sld:Title>8530 медь</sld:Title>
          <sld:Abstract>A 1 pixel wide black line</sld:Abstract>
          <ogc:Filter>
            <ogc:PropertyIsEqualTo>
              <ogc:PropertyName>cable_type</ogc:PropertyName>
              <ogc:Literal>59</ogc:Literal>
            </ogc:PropertyIsEqualTo>
          </ogc:Filter>
          <sld:MinScaleDenominator>8529.0</sld:MinScaleDenominator>
          <sld:MaxScaleDenominator>8531.0</sld:MaxScaleDenominator>
          <sld:LineSymbolizer>
            <sld:Stroke>
              <sld:CssParameter name="stroke">#FF0000</sld:CssParameter>
            </sld:Stroke>
          </sld:LineSymbolizer>
        </sld:Rule>
        <sld:Rule>
          <sld:Title>17061 медь</sld:Title>
          <sld:Abstract>A 1 pixel wide black line</sld:Abstract>
          <ogc:Filter>
            <ogc:PropertyIsEqualTo>
              <ogc:PropertyName>cable_type</ogc:PropertyName>
              <ogc:Literal>59</ogc:Literal>
            </ogc:PropertyIsEqualTo>
          </ogc:Filter>
          <sld:MinScaleDenominator>17060.0</sld:MinScaleDenominator>
          <sld:MaxScaleDenominator>68248.0</sld:MaxScaleDenominator>
          <sld:LineSymbolizer>
            <sld:Stroke>
              <sld:CssParameter name="stroke">#FF0000</sld:CssParameter>
              <sld:CssParameter name="stroke-width">0.5</sld:CssParameter>
            </sld:Stroke>
          </sld:LineSymbolizer>
        </sld:Rule>
        <sld:Rule>
          <sld:Title>Неизвестный</sld:Title>
          <sld:Abstract>A 1 pixel wide black line</sld:Abstract>
          <ogc:Filter>
            <ogc:PropertyIsEqualTo>
              <ogc:PropertyName>cable_type</ogc:PropertyName>
              <ogc:Literal>63</ogc:Literal>
            </ogc:PropertyIsEqualTo>
          </ogc:Filter>
          <sld:MinScaleDenominator>2131.0</sld:MinScaleDenominator>
          <sld:MaxScaleDenominator>68248.0</sld:MaxScaleDenominator>
          <sld:LineSymbolizer>
            <sld:Stroke>
              <sld:CssParameter name="stroke">#FF0000</sld:CssParameter>
              <sld:CssParameter name="stroke-width">4</sld:CssParameter>
              <sld:CssParameter name="stroke-dasharray">4.0 4.0</sld:CssParameter>
            </sld:Stroke>
          </sld:LineSymbolizer>
        </sld:Rule>
      </sld:FeatureTypeStyle>
    </sld:UserStyle>
  </sld:NamedLayer>
</sld:StyledLayerDescriptor>
