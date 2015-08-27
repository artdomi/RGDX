@ECHO OFF
set INTERVAL=1
:PINGINTERVAL
vlc --dshow-vdev="Logitech HD Pro Webcam C920" --dshow-size=1280x720 --dshow-aspect-ratio="16\:9" -V dummy --intf=dummy --dummy-quiet --video-filter=croppadd:scene --croppadd-croptop=250 --croppadd-cropbottom=320 --croppadd-cropleft=200 --croppadd-cropright=200 --scene-path=C:\inetpub\wwwroot\RGDX --scene-format=jpeg --scene-prefix=live --scene-replace --run-time=0.5 --scene-ratio=10 "dshow://" vlc://quit
timeout %INTERVAL%
GOTO PINGINTERVAL