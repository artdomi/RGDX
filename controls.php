<?PHP
session_start();
require_once("./include/membersite_config.php");
$fgmembersite->DBLogin();
$sessionid = session_id();
$sessionip = $_SERVER['REMOTE_ADDR'];
$AccessKey = 0;
$Superuser = 0;
if ($fgmembersite->CheckValidUser()) {
    $fgmembersite->ChangeAccessKey();
    $AccessKey=$fgmembersite->GetAccessKey();
    if ($fgmembersite->VerifySuperuser()) {
        $Superuser = 1;
    }
// if ($fgmembersite->UserInQueue()) {
}
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>RGDX Controls</title>
    <style>
          #container {
            height: 100%;
            width: 100%;
            min-width:400px;
            background: #757575;
            /*z-index: 0;*/
          }
        .sliderTextDiv {
            /*cursor: pointer;*/
            position: absolute; /*newly added*/
            display: block;
            /*float: right;*/
            margin: 0px;
            padding: 0px;
            /*z-index: 3;*/
            /*left: 40px; newly added*/
            /*top: 120px;newly added*/
            /*width:50px;*/
            /*height:20px;*/
            /*color: white;*/
            outline: none;
            border:none;
            /*border:1px solid lightgray;*/
            z-index: 1;
        }
        .sliderTextInput {
            display: block;
            padding-right: 10px;
            padding-top: 0px;
            font-weight: lighter;
            border:none;
            /*color: white;*/
            outline: none;
            text-align: right;
            -webkit-appearance: none;
            background-color: transparent;
            /*border:1px solid lightgray;*/
            -webkit-box-sizing: border-box; /* Safari/Chrome, other WebKit */
            -moz-box-sizing: border-box;    /* Firefox, other Gecko */
            box-sizing: border-box;         /* Opera/IE 8+ */
            z-index: 1;
        }
        .sliderAxis{
            position: absolute; /*newly added*/
            display: block;
            /*align: "center";*/
            text-align: center;
            padding: 0px;
            margin: 0 auto;
            font-weight: lighter;
            font-size: 14px;
            /*border:none;*/
            color: #d8d8d8;
            outline: none;
            -webkit-appearance: none;
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select:none;
            user-select:none;
            -o-user-select:none;
            z-index: 1;
        }
    </style>
    <!-- <link rel="stylesheet" href="bower_components/katex/dist/katex.min.css"> -->
    <!-- <link rel="stylesheet" href="js/katex/katex.min.css"> -->
    <!-- // <script src="js/katex/katex.min.js"></script> -->
    <!-- // <script src="bower_components/katex/dist/katex.min.js"></script> -->
</head>

<body>

    <div id="container"></div>
    <!-- // <script src="js/jquery-2.1.3.min.js"></script> -->
    <!-- // <script src="js/jquery.mobile-1.4.5.min.js"></script> -->
    <script src="js/konva.min.js"></script>
    <!-- // <script src="js/ui.js"></script> -->
    <script src="js/math.js"></script>
    <script src="js/ui.js"></script>
    <script src="js/slider.js"></script>
    <script src="js/button.js"></script>
    <script defer="defer">

//==============================================================================
//                                  PARAMETERS
//==============================================================================

    //EXPERIMENT VARIABLES
    var Pressure =      {value: 10, valueRead: 0,    name: "Pressure",       min: 0, max: 1000, units: '', colour: '#80fb8f'};//80fb8f //9eff86
    var Electrode =     {value: 0,                  name: "Electrode",      min: 0, max: 2000, units: '', colour: '#63e7ff'};//6fb6ff //63e7ff
    var Electromagnet = {value: 0,                  name: "Electromagnet",  min: 0, max: 200 , units: '', colour: '#ffac8a'};//ff7575 //ffac8a
    var MoveElectrode = {value: 0.5, valueRead: 0.5,name: "MoveElectrode",  min: 0.5, max: 4.0 , units: '', colour: '#ffac8a'};//ff7575 //ffac8a
    var realElectrode = {value: 76,                 name: "realElectrode",  min: 0.5, max: 4.0 , units: '', colour: '#ffac8a'};//ff7575 //ffac8a
    var MoveHC =        {value: 0.5, valueRead: 0.5,name: "MoveHC",         min: 0.5, max: 4.0 ,   units: '', colour: '#ffac8a'};//ff7575 //ffac8a
    var realHC =        {value: 5,                  name: "realHC",         min: 0.5, max: 4.0 ,  units: '', colour: '#ffac8a'};//ff7575 //ffac8a
    var Photodiode =    {value: 0,                  name: "Photodiode",     min: 0, max: 20 ,  units: '', colour: '#ffac8a'};//ff7575 //ffac8a


    var lights = {value: 1, name: "Lights", colour: "#f9ff45"};
    // var AccessKey = {value: 1234, name: "AccessKey"};
    var AccessKey = {value: <?PHP echo $AccessKey;?>};
    var AccessAuth = {value: 0, name: "AccessAuth"};
    var Superuser = {value: <?PHP echo $Superuser;?>};
    // console.log('Superuser: ' + Superuser.value);
    var lightStrip = new Array();
    var lightUpPlasma = false;
    var justOnce = false;
    //POSITION FOR THE CONTROLS
    CTRLS   = [{x: 10, y:30, w: 300, h: 60, text: 'Pressure (mTorr):', incr: 'true', comm: 'bi', style:"2", op:0.6, bg: "#dfdfdf", axis: 'log'}, //bg;
               {x: 330, y:30, w: 300, h: 60, text: 'Electromagnet (Gauss):',     incr: 'true', comm: 'mono', style:"2", op:0.6, bg: "#dfdfdf", axis: 'linear'},
               {x: 10, y:320, w: 500, h: 60, text: 'Electrode Voltage (Volts):', incr: 'true', comm: 'mono', style:"2", op:0.6, bg: "#dfdfdf", axis: 'linear'},
               {x: 80, y:172, w: 470, text: 'Electrode Position (cm):'},
               {x: 80, y:120, w: 470, text: 'Electromagnet Position (cm):'},
               {x: 530, y:320, w: 100, h: 75, text: 'Lights', style:"1"}];

    var time = 0;
    var framerate = 100; // in ms, movement every N ms
    var oldtime = 0;
    var newtime = 0;
    var changing = 0;
    var time_changing = 0;
    var Timer = 0;

    //IMAGE SOURCES
    var sources = {
        bg: "img/RGDX_BGv2_2.png",
        magnet: "img/magnet2.png",
        plasma: "img/plasmav2.png",
        // live: "http://scied-web.pppl.gov/rgdx/live.jpeg"
    };

    var staticLayer = new Konva.Layer();
    var controlsStaticLayer = new Konva.Layer();
    var controlsDynamicLayer = new Konva.Layer();
    var buttonStaticLayer = new Konva.Layer();
    var vectorlayer = new Konva.Layer();
    var videoLayer = new Konva.Layer();
    var motionLayer = new Konva.Layer();


//==============================================================================
//                                  XML Loader
//==============================================================================

    function loadXMLDoc(dname)
    {
        if (window.XMLHttpRequest)
          {
          xhttp=new window.XMLHttpRequest();
          }
        else
          {
          xhttp=new ActiveXObject("Microsoft.XMLHTTP");
          }
        xhttp.open("GET",dname,false);
        xhttp.send();
        return xhttp.responseXML;
    }

    var myWidth = 640,
        myHeight = 400;

    var stage = new Konva.Stage({
        container: 'container',
        width: myWidth,
        height: myHeight,
    });

//==============================================================================
//                                BUILD THE SCENE
//==============================================================================

    //IMAGE LOADER (DO THIS FIRST BEFORE PROCESSING SCENE)
    function loadImages(sources, callback) {
      var images = {};
      var loadedImages = 0;
      var numImages = 0;
      // get num of sources
      for(var src in sources) {
        numImages++;
      }
      for(var src in sources) {
        images[src] = new Image();
        images[src].onload = function() {
          if(++loadedImages >= numImages) {
            callback(images);
          }
        };
        images[src].src = sources[src];
      }
    }

    //WHAT TO DO WITH THE LOADED IMAGES
    function draw(images) {
        var rgdxMagnet = new Konva.Image({
            x: CTRLS[4].x,
            y: CTRLS[4].y,
            image: images.magnet,
            width: 56,
            height: 141,
            draggable: (Superuser.value == 1) ? true : false,
            dragBoundFunc: function(pos) {
              return {
                x: (pos.x < CTRLS[4].x) ? (CTRLS[4].x) : ((pos.x > CTRLS[4].x+CTRLS[4].w) ? CTRLS[4].x+CTRLS[4].w : pos.x),
                y: this.getAbsolutePosition().y
                // x: (pos.x < 130) ? (130) : ((pos.x > 450) ? 450 : pos.x),
                // y: this.getAbsolutePosition().y
              }
            },
            brightness: 0.1,
            id: "rgdxMagnet"
        });
        rgdxMagnet.cache();
        rgdxMagnet.filters([Konva.Filters.Brighten]);
        var rgdxMagnet_shadow = new Konva.Image({
            x: CTRLS[4].x,
            y: CTRLS[4].y,
            image: images.magnet,
            width: 56,
            height: 141,
            opacity: (Superuser.value == 1) ? 0.2 : 0,
            id: "rgdxMagnetShadow"
        });

        if (Superuser.value == 1) {
            rgdxMagnet.on('mouseover', function() {
                document.body.style.cursor = 'pointer';
                this.brightness(0.15);
            });

            rgdxMagnet.on('mouseout', function() {
              document.body.style.cursor = 'default';
              this.brightness(0.1);
            });
            rgdxMagnet.on('dragmove', function() {
                // var magnet_x = 0.5 + (320 - (this.x() - 130))/320*4;
                var magnet_x = MoveHC.min + (this.x() - CTRLS[4].x) / CTRLS[4].w * (MoveHC.max-MoveHC.min);
                // console.log('write magnet: ' + magnet_x);
                if (magnet_x > MoveHC.max) magnet_x = MoveHC.max;
                if (magnet_x < MoveHC.min) magnet_x = MoveHC.min;
                MoveHC.value = magnet_x;
            });
        }
        var rgdxLive = new Konva.Rect({
            x: 120,
            y: 120,
            width: 400,
            height: 100,
            fill: 'white',
            opacity: 0.8,
            id: "rgdxLive"
        });

        var rgdxBG = new Konva.Image({
            x: 0,
            y: -20,
            image: images.bg,
            width: 640,
            height: 500,
            // blurRadius: 20
        });
        var plasmaBG = new Konva.Image({
            x: 75,
            y: 125,
            image: images.plasma,
            width: 484,
            height: 94,
            opacity: 0,
            id: 'plasma'
        });

        // rgdxBG.cache();
        // rgdxBG.filters([Konva.Filters.Blur]);

        // rgdxBG.tween = new Konva.Tween({
        //     node: rgdxBG,
        //     blurRadius: 0,
        //     // fill: '#47ff5f',
        //     easing: Konva.Easings.StrongEeaseIn,
        //     duration: 0.5,
        //     // onFinish: function() {
        //     //     // this.reverse();
        //     // }
        // });

        // videoLayer.add(rgdxLive);
        videoLayer.add(plasmaBG);
        motionLayer.add(rgdxMagnet_shadow);
        motionLayer.add(rgdxMagnet);
        staticLayer.add(rgdxBG);

        buildScene();

        buildControls();

        buildListeners();

        stackLayers();


        // rgdxBG.tween.play();
        // stage.batchDraw();

        anim.start();

        // setInterval(function(){
        //     $("#rgdxLive").attr("src", images.live);
        //     updateMotion();
        // },1000)
    }


    function updateVideo() {
        var imageObj = new Image();
            imageObj.onload = function() {
              // var img = new Konva.Image({
              //   x: 0,
              //   y: 0,
              //   image: imageObj,
              //   width: 640,
              //   height: 500
              // });
              // // add the shape to the layer
              // staticLayer.add(yoda);
              // add the layer to the stage
              // stage.add(layer);
              videoLayer.find('#rgdxLive')[0].image(imageObj);
              videoLayer.batchDraw();
            };
        imageObj.src = sources.live;
    }

    function buildScene() {

        var electrode = new Konva.Rect({
            x: 500,
            y: 172,
            width: 10,
            height: 90,
            offset: {x: 5, y: 45},
            fill : '#b8b8b8',
            opacity : 1,
            draggable: (Superuser == 1) ? true : false,
            dragBoundFunc: function(pos) {
                      return {
                        x: (pos.x < 150) ? (150) : ((pos.x > 500) ? 500 : pos.x),
                        y: this.getAbsolutePosition().y
                      }
                    },
            stroke: '#9d9d9d',
            strokeWidth: 1,
            cornerRadius: 5,
            id: 'electrode',
        });
        var electrode_shadow = new Konva.Rect({
            x: 500,
            y: 172,
            width: 10,
            height: 90,
            offset: {x: 5, y: 45},
            fill : '#b8b8b8',
            opacity : (Superuser.value == 1) ? 0.5 : 0,
            stroke: '#9d9d9d',
            strokeWidth: 1,
            cornerRadius: 5,
            id: 'electrodeShadow',
        });
        var electrode_arm = new Konva.Rect({
            x: 510,
            y: 192,
            width: 10,
            height: 10,
            offset: {x: 5, y: 5},
            fill : '#b8b8b8',
            opacity : 1,
            draggable: true,
            dragBoundFunc: function(pos) {
                      return {
                        x: (pos.x < 150) ? (150) : ((pos.x > 500) ? 500 : pos.x),
                        y: this.getAbsolutePosition().y
                      }
                    },
            stroke: '#9d9d9d',
            strokeWidth: 1,
            cornerRadius: 5,
            id: 'electrode',
        });
        if (Superuser.value == 1) {
            electrode.on('mouseover', function() {
                document.body.style.cursor = 'pointer';
                this.stroke("gray");
            });

            electrode.on('mouseout', function() {
              document.body.style.cursor = 'default';
              this.stroke("#9d9d9d");
            });
            electrode.on('dragmove', function() {
                var electrode_x = 0.5 + (350 - (this.x() - 150))/350*4;
                // console.log('write electrode: ' + electrode_x);
                if (electrode_x > 4) electrode_x = 4;
                if (electrode_x < 0.5) electrode_x = 0.5;
                MoveElectrode.value = electrode_x;
            });
        }
        lightStrip.push( {x: 130, y: 225, name: 'lights'},
                         {x: 150, y: 225, name: 'lights'},
                         {x: 170, y: 225, name: 'lights'},
                         {x: 190, y: 225, name: 'lights'},
                         {x: 210, y: 225, name: 'lights'},
                         {x: 230, y: 225, name: 'lights'},
                         {x: 250, y: 225, name: 'lights'},
                         {x: 270, y: 225, name: 'lights'},
                         {x: 290, y: 225, name: 'lights'},
                         {x: 310, y: 225, name: 'lights'},
                         {x: 330, y: 225, name: 'lights'},
                         {x: 350, y: 225, name: 'lights'},
                         {x: 370, y: 225, name: 'lights'},
                         {x: 390, y: 225, name: 'lights'},
                         {x: 410, y: 225, name: 'lights'},
                         {x: 430, y: 225, name: 'lights'},
                         {x: 450, y: 225, name: 'lights'},
                         {x: 470, y: 225, name: 'lights'},
                         {x: 490, y: 225, name: 'lights'});

        //======================================================================
        //                          LIGHT CONSTRUCTORS
        //======================================================================
        // function buildLights() {
            // if (!start_index) start_index=0;
            for (var i = 0; i <= (lightStrip.length-1); i++) {
                var node = new Konva.Circle({
                    x: lightStrip[i].x,
                    y: lightStrip[i].y,
                    radius: 3,
                    fill: '#fbff9d',
                    shadowColor: '#fbff9d',
                    shadowBlur: 40,
                    shadowOffset: {x : 0, y : 0},
                    shadowOpacity: 1,
                    name: String(lightStrip[i].name),
                });
                videoLayer.add(node);
            };
        // }
        var consoleText = new Konva.Text({
          x: 250,
          y: 150,
          text: 'Hello, Dave.',
          fontSize: 30,
          fontFamily: 'Helvetiva Neue, Helvetica, Calibri',
          fill: 'white'
        });
        var consoleTween = new Konva.Tween({
          node: consoleText,
          duration: 3,
          opacity: 0,
          fill: 'black',
          easing: Konva.Easings.EaseInOut,
          onFinish: function() {
            consoleText.destroy();
          }
        });

        if (Superuser.value == 1) {
            motionLayer.add(consoleText);
            // console.log("Hello, Dave.");
            consoleTween.play();
            // consoleTween.onFinish();
        }
        // console.log(Superuser);
        motionLayer.add(electrode_shadow);
        motionLayer.add(electrode);
    }

//==============================================================================
//                              UPDATE PARAMETERS
//==============================================================================

    function updateMotion(){
        changing = 1;
        // MoveHC.value = realHC.value/10.;
        // MoveElectrode.value = 4.5- 4./76.* realElectrode.value;
    }

    function updateLights(){
        updateMotion();

        var allLights = stage.find('.lights');
        // console.log(lights.value);
        switch(lights.value) {
            case 1:     allLights.fill('#e8ed64');
                        break;
            case 0:     allLights.fill('#363636');
                        break;
        }
        videoLayer.batchDraw();

    }

//==============================================================================
//                                  CONTROLS
//==============================================================================

    function buildControls() {

        Slider(Pressure,      updateMotion, CTRLS[0], controlsStaticLayer, controlsDynamicLayer, "controls");
        Slider(Electromagnet, updateMotion, CTRLS[1], controlsStaticLayer, controlsDynamicLayer, "controls");
        Slider(Electrode,     updateMotion, CTRLS[2], controlsStaticLayer, controlsDynamicLayer, "controls");
        // Slider(realElectrode, updateMotion, CTRLS[3], controlsStaticLayer, controlsDynamicLayer, "controls");
        // Slider(realHC,        updateMotion, CTRLS[4], controlsStaticLayer, controlsDynamicLayer, "controls");

        Button(lights, updateLights, CTRLS[5], buttonStaticLayer);

    }

//==============================================================================
//                                  LISTENERS
//==============================================================================


    function buildListeners() {

        // buttonLayer.find('.button1').on('mousedown touchstart', function() {
        //      updateMotion();
        //      updateLights('flick');
        //  });
    }

//==============================================================================
//                            STACK LAYERS ON THE STAGE
//==============================================================================

    function stackLayers() {
        stage.add(videoLayer);
        stage.add(staticLayer);
        stage.add(motionLayer);
        stage.add(buttonStaticLayer);
        stage.add(controlsStaticLayer);
        stage.add(controlsDynamicLayer);
        stage.batchDraw();
    }

//==============================================================================
//                                  ANIMATION
//==============================================================================

    var anim = new Konva.Animation(function(frame) {

        newtime = frame.time;
        if ((newtime-oldtime) >= framerate){

            // time = time + framerate/1000;
            var timediff = newtime-oldtime;
            oldtime = newtime;
            time_changing += timediff;

            

            // if ((changing == 1) && (time_changing >= 500)) {
            if (time_changing >= 500) {
                time_changing = 0;      //reset clock
                changing = 0;           //reset to static status

                // updateVideo();

                //http://scied-web.pppl.gov:8080/rgdx_service/rgdx_control/2000/1000/100/0

                //###########WORKING
                // url=("http://scied-web.pppl.gov:8080/rgdx_service/rgdx_control/"+Electrode.value+"/"+Electromagnet.value+"/"+Pressure.value+"/"+lights.value+"/"+AccessKey.value);

                // console.log('***** : ' + MoveElectrode.value);
                url=("http://scied-web.pppl.gov:8080/rgdx_service/rgdx_control/"+Electrode.value+"/"+Electromagnet.value+"/"+Pressure.value+"/"+lights.value+"/"+MoveElectrode.value+"/"+MoveHC.value+"/"+AccessKey.value);
                // /RGDX_control/:DC_V_write/:HC_I_write/:Press_write/:Lights/:Electrode_pos_write/:HC_pos_write/:Key
                // url=("http://scied-web.pppl.gov:8080/rgdx_service/rgdx_control/0/0/0/0/1/0/1");
                // url=("/Users/liu/Desktop/test.xml");

                // url=("http://scied-web.pppl.gov:8080/rgdx_service/rgdx_control/"+Electrode.value+"/"+'0'+"/"+'0'+"/"+'0');
                // console.log(url);
                // var url=("http://scied-web.pppl.gov:8080/testservice/control_test/"+window[Electrode]+"/"+window[Pressure]);

                //var url=("http://scied-web.pppl.gov:8080/testservice/control_test2/"+window[Electrode]+"/"+window[Pressure]);
                //console.log(url);
                // lightsUrl=((lights==0) ? URL_lights_OFF : URL_lights_ON);
                // xmlhttp.open("GET",((lights==0) ? URL_lights_OFF : URL_lights_ON),true);
                // xmlhttp.send();

                // console.log(url);

//                     $.ajax({
//                        type: "GET",
//                        url: url,
//                        dataType: "xml",
//                        success: function(xml){
//                        $(xml).find('Terminal').each(function(){
//                          var sTitle = $(this).find('Name').text();
//                          var sValue = $(this).find('Value').text();
//                          if (sTitle == 'Press_read') {
//                             Pressure.valueRead = sValue;
//                             // console.log(sTitle + ': ' + Pressure.valueRead);
//                         }
//                        });
//                      },
//                      error: function() {
// //                        alert("An error occurred while processing XML file.");
//                      }
//                     });

                // LOAD THE THE XML DATA
                var xmlDoc=loadXMLDoc(url);
                $(xmlDoc).find('Terminal').each(function(){
                    var sTitle = $(this).find('Name').text();
                    var sValue = $(this).find('Value').text();
                    if (sTitle == 'Press_read') {
                        Pressure.valueRead = sValue;
                        // console.log(sTitle + ': ' + Pressure.valueRead);
                    }
                    if (sTitle == 'AccessOk') {
                        AccessAuth.value = sValue;
                        // console.log(sTitle + ': ' + Pressure.valueRead);
                    }
                    if (sTitle == 'Photodiode') {
                        Photodiode.value = sValue;
                        // console.log(sTitle + ': ' + Pressure.valueRead);
                    }if (sTitle == 'Electrode_pos_read') {
                        MoveElectrode.valueRead = sValue;
                        // console.log(sTitle + ': ' + MoveElectrode.valueRead);
                    }if (sTitle == 'HC_pos_read') {
                        MoveHC.valueRead = sValue;
                        console.log(sTitle + ': ' + MoveHC.valueRead);
                    }
                });



                var pressOut = parseInt(Pressure.valueRead);
                // SliderProgress.setWidth((variable.value-minValue)*density
                var magnet_x = 130 + (MoveHC.valueRead - 0.5)/4*320;
                if (magnet_x < 130) magnet_x = 130;
                if (magnet_x > 450) magnet_x = 450;
                var electrode_x = 150 + 350 - (MoveElectrode.valueRead - 0.5)/4*350;
                if (electrode_x < 150) electrode_x = 150;
                if (electrode_x > 500) electrode_x = 500;
                motionLayer.find('#electrodeShadow').setX(electrode_x);
                motionLayer.find('#rgdxMagnetShadow').setX(magnet_x);
                controlsDynamicLayer.find('#sliderPressureProgress').setWidth(Math.round(pressOut*150/(1000-0)));
                // motionLayer.find('#sliderPressureReadout').setText(parseInt(pressOut).toFixed(1));
                $('#sliderPressureReadout').val(pressOut);

                var plasmaopacity = Photodiode.value/10;
                if (plasmaopacity > 1) plasmaopacity = 1;
                lightUpPlasma = Photodiode.value > 7 && lights.value == 0
                videoLayer.find('#plasma').opacity( (lightUpPlasma ? plasmaopacity : 0));
                // alert(lightUpPlasma)
                if lightUpPlasma {
                    // if !justOnce {
                    //     console.log("V= " + Electrode.value + " P= " + Pressure.value + " D= " + MoveElectrode.valueRead);
                    //     justOnce = true;
                    // }
                } else lightUpPlasma = false;
                console.log(Photodiode.value);
                videoLayer.find('#rgdxLive').fill( (Photodiode.value > 10 ? 'pink' : 'white'));
                videoLayer.batchDraw();
                // controlsDynamicLayer.find('#sliderPressureReadout').setText(((pressOut >= 100) ? pressOut.toFixed(0) : pressOut.toFixed(1)));

            }

        }

    }, motionLayer);


//==============================================================================
//                                    BEGIN
//==============================================================================

    //LOAD ALL THE IMAGES AND CALL THE SCENE DRAWING FUNCTIONS WITH IT
    loadImages(sources, function(images) {
      draw(images);
    });

//==============================================================================
//                                     END
//==============================================================================

    </script>

</body>

</html>