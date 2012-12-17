jsPlumb.bind("ready", function() {
        var newMode = jsPlumb.setRenderMode('canvas');
        jsPlumbDemo.init();
    });

;(function() {
    window.jsPlumbDemo = {
        init : function() {
            jsPlumb.DefaultDragOptions = { cursor: "pointer", zIndex:2000 };

            var stateMachineConnector = {               
                connector:"Bezier",
                paintStyle:{lineWidth:3,strokeStyle:"silver"},
                hoverPaintStyle:{lineWidth:5,strokeStyle:"red"},
                endpoint:"Blank",
                anchor:"RightMiddle"
            };
            
            jsPlumb.connect({
                source:"window1",
                target:"window2"
            }, stateMachineConnector);
        }
    };  
    alert("sadas");
})();
