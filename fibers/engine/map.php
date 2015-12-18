<?

    include_once ('setup.php');
    include_once ('db.php');

    $nodes = array();
    $title;
    //$nodes[] = array('n1_id'=>'320','n2_id'=>'321','n1'=>'qqq','n2'=>'ww');

function add_arr($node_id_1,$node_id_2,$n1_id,$n2_id,$n1,$n2,$cable_name,$cable_fib)
{
    global $nodes;
    foreach ($nodes as $key => $value) {
        if($value['n1_id']==$n1_id && $value['n2_id']==$n2_id) return false;
    }
    $nodes[] = array('node_id_1'=>$node_id_1,'node_id_2'=>$node_id_2,'n1_id'=>$n1_id,'n2_id'=>$n2_id,'n1'=>$n1,'n2'=>$n2,'cable_name'=>$cable_name,'cable_fib'=>$cable_fib);
    return true;
}

function get_node_map($node,$node_old)
{
    global $table_cable;
    global $table_pq;
    global $table_node;
    global $title;
    global $table_cable_type;

    $sql = "SELECT n1.id AS node_id_1, n2.id AS node_id_2, n1.address AS n1, n2.address AS n2, n1.id AS n1_id, n2.id AS n2_id, c_t.name AS cable_name, c_t.fib AS cable_fib
        FROM ".$table_pq." AS p1, ".$table_pq." AS p2, ".$table_cable." AS c1, ".$table_node." AS n1, ".$table_node." AS n2, ".$table_cable_type." AS c_t
        WHERE ".(isset($_GET['id'])?"p1.node = ".$node." AND p2.node != ".$node_old." AND":"")." p2.id = CASE WHEN c1.pq_1 = p1.id THEN c1.pq_2 ELSE CASE WHEN c1.pq_2 = p1.id THEN c1.pq_1 ELSE NULL END END
        AND p1.node = n1.id
        AND p2.node = n2.id
    	AND c1.cable_type = c_t.id";
    //echo $sql.'<br>';
    //die;
    $result = pg_query($sql);
    if (pg_num_rows($result)) {
        while ($row = pg_fetch_assoc($result)) {
            if($node_old==0) $title=$row['n1'];
            if(isset($_GET['id'])) {
            if(add_arr($row['node_id_1'],$row['node_id_2'],$row['n1_id'],$row['n2_id'],$row['n1'],$row['n2'],$row['cable_name'],$row['cable_fib']) && $row['n2_id'])
                get_node_map($row['n2_id'],$row['n1_id']);
            } else add_arr($row['node_id_1'],$row['node_id_2'],$row['n1_id'],$row['n2_id'],$row['n1'],$row['n2'],$row['cable_name'],$row['cable_fib']);
        }
    }
    return $content;
}

    $content=get_node_map($_GET['id'],0);
    foreach ($nodes as $key => $value) {
    	//$content.="graph.addNode('".$value['n1']."', '91bad8ceeec43ae303790f8fe238164b','sssssssss');";
    	$content.="graph.addNode('".$value['n1']."',['favicon.ico','".$value['node_id_1']."']);";
    	$content.="graph.addNode('".$value['n2']."',['favicon.ico','".$value['node_id_2']."']);";
        $content.="graph.addLink('".$value['n1']."', '".$value['n2']."',['".$value['cable_name']."','".$value['cable_fib']."']);";
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title><?echo $title?></title>
    <script type="text/javascript" src="../js/vivagraph/vivagraph.js"></script>
    <script type="text/javascript" src="../js/lib/jquery-1.7.1-min.js"></script>
    <!--<script type="text/javascript" src="js/lib/jquery.poshytip.js"></script>-->
    <script type="text/javascript">
        function main () {
           var graph = Viva.Graph.graph();

           var layout = Viva.Graph.Layout.forceDirected(graph, {
               springLength : 110,
               springCoeff : 0.0008,
               dragCoeff : 0.02,
				gravity : -50.2
			});

			<? echo $content; ?>

			var graphics = Viva.Graph.View.svgGraphics(),
			nodeSize = 24,
			hint = function(nodeId, over) {
				graph.forEachLinkedNode(nodeId, function(node, link){
					if (link && link.ui) {
						//link.ui.attr('stroke', over ? 'red' : 'gray');
						alert('dddd');
					}
				});
			};

            graphics.node(function(node) {
              // This time it's a group of elements: http://www.w3.org/TR/SVG/struct.html#Groups
              	var svgText = Viva.Graph.svg('text').attr('y', '-5').attr('x','-'+node.id.length*3).text(node.id);
              	var img = Viva.Graph.svg('image')
	                .attr('width', nodeSize)
	                .attr('height', nodeSize)
	                //.attr('stroke', 'red')
	                //.attr('stroke-dasharray', '5, 5')
	                .link('http://www.hutor.ru/' + node.data[0]);

                var ui = Viva.Graph.svg('g');

                /*$(ui).hover(function() { // mouse over
                    highlightRelatedNodes(node.id, true);
                }, function() { // mouse out
                    highlightRelatedNodes(node.id, false);
                });*/
                $(ui).dblclick(function(e) { // mouse over
                    window.open('http://pto.rdtc.ru/fibers/index.php?act=s_pq&o_node&node_id='+node.data[1]);
                	/*var ttt = node;
					var temp; for(key in ttt) {temp += key + " = " + ttt[key] + "\n";} alert(temp);
                    alert(temp);*/
                });
                ui.append(svgText);
                ui.append(img);
                //ui.append('title').text(node.id);
                ui.append('link').text(node.id);
                return ui;
            }).placeNode(function(nodeUI, pos) { 
                nodeUI.attr('transform', 
                            'translate(' + 
                                  (pos.x - nodeSize/2) + ',' + (pos.y - nodeSize/2) + 
                            ')');
            });

            graphics.link(function(link){
                var color;
                //http://www.w3schools.com/cssref/css_colornames.asp
                var fib=link.data[1];
                color='silver';
                if(fib==2) color='<? echo $cable_color['cable_2']; ?>';
                if(fib==4) color='<? echo $cable_color['cable_4']; ?>';
                if(fib==6) color='<? echo $cable_color['cable_6']; ?>';
				if(fib==8) color='<? echo $cable_color['cable_8']; ?>';
				if(fib==16) color='<? echo $cable_color['cable_16']; ?>';
				if(fib==24) color='<? echo $cable_color['cable_24']; ?>';
				if(fib==32) color='<? echo $cable_color['cable_32']; ?>';
				if(fib==48) color='<? echo $cable_color['cable_48']; ?>';
				if(fib==64) color='<? echo $cable_color['cable_64']; ?>';
				if(fib==96) color='<? echo $cable_color['cable_96']; ?>';

				var ui = Viva.Graph.svg('path')
							.attr('stroke', color)
							//.attr('stroke-dasharray', '10, 10')
                			//.attr('stroke', '#999')
                			//.attr('text').text('222')
                			.attr('stroke-width', Math.sqrt(link.data[1]*3));
				$(ui).click(function(e) { // mouse over
                    //hint(node.id,true);
                    /*var ttt = link['data'];
                    //var ttt = this;
					var temp; for(key in ttt) {temp += key + " = " + ttt[key] + "\n";} alert(temp);
                    alert(temp);*/
                    alert(link['data'][0]);
                    /*mouseX = e.pageX; 
                    mouseY = e.pageY;
                    alert(mouseX+' '+mouseY);*/
                });
                //var svgText = Viva.Graph.svg('text').attr('y', '-5').attr('x','-'+node.id.length*3).text('sss');

                $(ui).hover(function() { // mouse over
                	svgText.attr("display", "block");
                }, function() { // mouse out
                	svgText.attr("display", "none");
                });
    			return ui;
            }).placeLink(function(linkUI, fromPos, toPos) {
                var data = 'M' + fromPos.x + ',' + fromPos.y + 
                           'L' + toPos.x + ',' + toPos.y;
                linkUI.attr("d", data);
            });

            var renderer = Viva.Graph.View.renderer(graph,
                {
                    layout     : layout,
                    graphics   : graphics,
                    container  : document.getElementById('graph1'),
                    renderLinks : true,
                    prerender  : true
                });
                
            renderer.run();
            //g = graph;
        }
    </script>
    
    <style type="text/css" media="screen">
        html, body, svg { width: 100%; height: 100%; position: absolute; margin:0px;}
    </style>
</head>
<body onload='main()'>
    <svg xmlns="http://www.w3.org/2000/svg"
	     xmlns:xlink="http://www.w3.org/1999/xlink">
	<?
	$nodes_tmp = array();
	foreach ($nodes as $key => $value) {
		$nodes_tmp[$value['cable_fib']]=$value['cable_name'];
	}
	natsort($nodes_tmp);
	$i=1;
	foreach ($nodes_tmp as $key => $value) {
		//echo $key.' '.$value.'<br>';
		eval("\$color = \$cable_color['cable_$key'];");
		echo '<line x1="10"  y1="'.($i*20-5).'" x2="30" y2="'.($i*20-5).'" style="stroke:'.$color.';stroke-width:5"/>';
		echo '<text x="35" y="'.($i*20).'">'.$value.'</text>';
		$i++;
	}
	?>
	</svg>
</body>
</html>