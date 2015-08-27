function ScrollToContent() {
    document.getElementById("content").style.visibility = "visible";
    $("#content").fadeIn(300);
    $('html, body').animate({
                scrollTop: ($('#content').offset().top-70)
            }, 1000);
}

//==========UPDATE STATUS, QUEUE TABLE AND LOAD CONTROLS AFTER WAIT=========
function refreshTable(){
    $('.StatusInfo').load('./getTime.php', function(){
       setTimeout(refreshTable, 3000);
    });
}

$.urlParam = function(name){
    var results = new RegExp('[\\?&]' + name + '=([^&#]*)').exec(window.location.href);
    if (results==null){
       return null;
    }
    else{
       return results[1] || 0;
    }
}

// POP-UP DIALOG GENERATOR
function generate(type, theme, message) {
    var n = noty({
        text        : message,
        type        : type,
        dismissQueue: true,
        layout      : theme == 'defaultTheme' ? 'topCenter' : 'topCenter',
        theme       : theme,
        // template: '<div class="noty_message"><span class="noty_text">asd</span><div class="noty_close">close</div></div>',
        // timeout     : 2000,
        closeWith   : ['button', 'click'],
        maxVisible  : 20,
        animation: {
            open: {height: 'toggle'},
            close: {height: 'toggle'},
            easing: 'easeOutBack',
            speed: 800 // opening & closing animation speed
        },
    });
    console.log('html: ' + n.options.id);
}

$(document).ready( function() {

    $('#nav > li > a').click(function(){
      if ($(this).attr('class') != 'open'){
        $('#nav li ul').slideUp({duration: "slow", easing:"easeOutBack"});
        $(this).next().slideToggle({duration: "slow", easing:"easeOutBack"});
        $('#nav li a').removeClass('open');
        $(this).addClass('open');
      }else{
        $(this).next().slideToggle({duration: "slow", easing:"easeOutBack"});
      }
    });

    $('.navbtn').click(function(e){
        e.preventDefault();
        ScrollToContent();
        $('#nav li ul li a').removeClass('open');
        // $("#content").load('./1_a.html');
        $(this).addClass('open');
        $("#content").load('pages/'+$(this).attr('id')+'.html');
    });


    $('#nav li a').first().addClass( "open" );
    $('#nav li ul').first().slideDown({duration: 1000, easing:"easeOutBack"});
    $('#nav li ul li a').first().addClass('open');

    var scriptString = "You're logged out!";
    $('.SignOut').click(function(){
        $("#controls").fadeOut(300);
        $("#Status").fadeOut(300);
        // $("#controls").hide();
        // $("#controls").load("./loginRGDX.php");
        $.ajax({
          method: 'get',
          url: 'kickout.php',
          data: {
            'myString': scriptString,
            'ajax': true
          },
          success: function(data) {
            $('#controls').load("./loginRGDX.php");
            // $('#controls').load("./TestControls.html");
            $("#controls").fadeIn(300);
            // $('#controls').text(data);
          }
        });
        // $("#Status").fadeOut(200);
        // $("#TaskHolder").fadeOut(200);
        // $("#content").fadeOut(200);
        // $("#controls").load("./loginRGDX.php");
        $('.StatusInfo').load('./getTime.php');
        $("#controls").animate({height:'130px'},"fast");
        // $("#controls").show();
    });


    // $('#controls').hover(function() {
    $('.LevelTab2')
    .hover(function() {
        $( '.LevelTab2' ).animate({
            // width: "70%",
            // opacity: 0.4,
            height: "40px",
            width: "130px",
            // marginBo: "-0.2in",
            // fontSize: "3em",
            // borderWidth: "10px"
          }, {duration: "fast", easing:"easeOutBack"} );
    // $('.LevelTab').click(function(){
        // $( this).animate({left:'50'},"slow");
      // $( this ).fadeOut( 100 );
      // $( this ).fadeIn( 500 );
    })
    .mouseleave(function() {
        $( '.LevelTab2' ).animate({
            // width: "70%",
            // opacity: 0.7,
            // marginTop: "0in",
            height: "30px",
            // fontSize: "3em",
            // borderWidth: "10px"
          }, {duration: "fast", easing:"easeOutBack"} );
    });




    // $("#Status").show( "puff",{ }, 750 );
    // $("#controls").show( "slide",{ }, 750 );
    // $('.rightcolumn').fixTo('container');
    // $('.rightcolumn').fixTo('body');
    // $('#nav').fixTo('body');

    // $('.leftcolumn').fixTo('body');
    $(".vidcontainer").load("./live.html");
    // document.getElementById("content").style.visibility = "hidden"
    $('#controls').hide();
    // $('#content').hide();
    $("#content").load('pages/1_a.html');

    refreshTable(); //START PERIODIC CONNECTION TO DB

    // var xyz = jQuery.param("playcode");
    var rgdxcode = $.urlParam('playcode');

    if (rgdxcode) {
        $.post(('updatecode.php?playcode='+rgdxcode));
        // alert('code is set by jquery!' + xyz);
    }

});