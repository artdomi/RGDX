// RGDX Debug code - REV0, March 4, 2015

//%%%%%%%%%%%%%%%%%%%%% Stepper microcontroler Parameters %%%%%%%%%%%%%%%%%%%%%

int dirpin = 2;    				// it's on a static pulse pin (direction input)
int steppin = 3;   				// it's on a PWM~ pin (velocity of step input)
int onoffpin = 4;  				// it's on a static pulse pin (on and off input)

//%%%%%%%%%%%%%%%%%%%%% Off Button Parameters %%%%%%%%%%%%%%%%%%%%%

boolean lastbutton1 = LOW;                       // Suplemental for the switch
boolean motoron = false;                        // Suplemental for the switch
boolean lastbutton2 = LOW;
boolean dir = false;
long PosPP[100]={0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0};
long PsPP[100]={0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0};
long D[100]={0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0};
long V[100]={0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0};
int i =0;
long SumPosP=0;
long SumPsP=0;
long Move=0;
long Signal=0;
int r=0;
int x=0;
int z=0;
int j=0;
int a=0;
int b=0;
int Vone=0;
int Vtwo=0;
int Vall=0;
int Select=0;
float Vb= 0;
float Vs= 0;
float RealSignal=0;
float n=0;
float m=0;
float q=0;


int currentSpeed = 400;
int Out1 = 740;
int Out2 = 30;



//%%%%%%%%%%%%%%%%%%%%% Potentiometer Parameters %%%%%%%%%%%%%%%%%%%%%

int potpin = A0;    			        // Potentiometer Pin
int PowSup = A1;
int PosP;
int PsP;
int SignalChange = 0;
int Change =0;
int Upper = 0;
int Lower = 0;
int y=0;
//boolean left = false;
//boolean right = true;

//%%%%%%%%%%%%%%%%%%%%% Failsafe Buttons Parameters %%%%%%%%%%%%%%%%%%%%%

const int switchpin1 = 7;		        // it's on a static pulse pin (failsafe length limit1)
const int switchpin2 = 8; 			// it's on a static pulse pin (failsafe length limit2)
long distSafeEnd = 300;  			// failsafe distance (Potentiometer position 300)
long distSafeStart = 100; 			// final safe distance (Potentiometer position 100)
int failState = 0;                              // failsafe counter 
int whichButton = 0;                            // Pushed button counter

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

void setup() {
  pinMode(dirpin, OUTPUT);     
  pinMode(steppin, OUTPUT);
  pinMode(onoffpin, OUTPUT);
  pinMode(switchpin1, INPUT);
  pinMode(switchpin2, INPUT);
  pinMode(potpin,INPUT);
  digitalWrite(dirpin, LOW);
  digitalWrite(steppin, LOW);
  digitalWrite(onoffpin, HIGH);
  Serial.begin(9600);
}


void loop() {
  int PosP = analogRead(potpin);
  int PsP = analogRead(PowSup);

  
  SumPosP= PosPP[0]+PosPP[1]+PosPP[2]+PosPP[3]+PosPP[4]+PosPP[5]+PosPP[6]+PosPP[7]+PosPP[8]+PosPP[9]+PosPP[10]+PosPP[11]+PosPP[12]+PosPP[13]+PosPP[14]+PosPP[15]+PosPP[16]+PosPP[17]+PosPP[18]+PosPP[19]+PosPP[20]+PosPP[21]+PosPP[22]+PosPP[23]+PosPP[24]+PosPP[25]+PosPP[26]+PosPP[27]+PosPP[28]+PosPP[29]+PosPP[30]+PosPP[31]+PosPP[32]+PosPP[33]+PosPP[34]+PosPP[35]+PosPP[36]+PosPP[37]+PosPP[38]+PosPP[39]+PosPP[40]+PosPP[41]+PosPP[42]+PosPP[43]+PosPP[44]+PosPP[45]+PosPP[46]+PosPP[47]+PosPP[48]+PosPP[49]+PosPP[50]+PosPP[51]+PosPP[52]+PosPP[53]+PosPP[54]+PosPP[55]+PosPP[56]+PosPP[57]+PosPP[58]+PosPP[59]+PosPP[60]+PosPP[61]+PosPP[62]+PosPP[63]+PosPP[64]+PosPP[65]+PosPP[66]+PosPP[67]+PosPP[68]+PosPP[69]+PosPP[70]+PosPP[71]+PosPP[72]+PosPP[73]+PosPP[74]+PosPP[75]+PosPP[76]+PosPP[77]+PosPP[78]+PosPP[79]+PosPP[80]+PosPP[81]+PosPP[82]+PosPP[83]+PosPP[84]+PosPP[85]+PosPP[86]+PosPP[87]+PosPP[88]+PosPP[89]+PosPP[90]+PosPP[91]+PosPP[92]+PosPP[93]+PosPP[94]+PosPP[95]+PosPP[96]+PosPP[97]+PosPP[98]+PosPP[99];
  SumPsP= PsPP[0]+PsPP[1]+PsPP[2]+PsPP[3]+PsPP[4]+PsPP[5]+PsPP[6]+PsPP[7]+PsPP[8]+PsPP[9]+PsPP[10]+PsPP[11]+PsPP[12]+PsPP[13]+PsPP[14]+PsPP[15]+PsPP[16]+PsPP[17]+PsPP[18]+PsPP[19]+PsPP[20]+PsPP[21]+PsPP[22]+PsPP[23]+PsPP[24]+PsPP[25]+PsPP[26]+PsPP[27]+PsPP[28]+PsPP[29]+PsPP[30]+PsPP[31]+PsPP[32]+PsPP[33]+PsPP[34]+PsPP[35]+PsPP[36]+PsPP[37]+PsPP[38]+PsPP[39]+PsPP[40]+PsPP[41]+PsPP[42]+PsPP[43]+PsPP[44]+PsPP[45]+PsPP[46]+PsPP[47]+PsPP[48]+PsPP[49]+PsPP[50]+PsPP[51]+PsPP[52]+PsPP[53]+PsPP[54]+PsPP[55]+PsPP[56]+PsPP[57]+PsPP[58]+PsPP[59]+PsPP[60]+PsPP[61]+PsPP[62]+PsPP[63]+PsPP[64]+PsPP[65]+PsPP[66]+PsPP[67]+PsPP[68]+PsPP[69]+PsPP[70]+PsPP[71]+PsPP[72]+PsPP[73]+PsPP[74]+PsPP[75]+PsPP[76]+PsPP[77]+PsPP[78]+PsPP[79]+PsPP[80]+PsPP[81]+PsPP[82]+PsPP[83]+PsPP[84]+PsPP[85]+PsPP[86]+PsPP[87]+PsPP[88]+PsPP[89]+PsPP[90]+PsPP[91]+PsPP[92]+PsPP[93]+PsPP[94]+PsPP[95]+PsPP[96]+PsPP[97]+PsPP[98]+PsPP[99];
  
  if (r == 0){
    for(x=0; x<100; x++)
    {
    PosPP[x]=PosPP[x]+PosP;
    PsPP[x]=PsPP[x]+PsP;
    }
    r=r+1;
  }
  //Serial.println(a);
  if (a == 0){
    
    currentSpeed = 1000;
    
    while (digitalRead(switchpin1) == LOW){
    runMotor(1, 200, true);
    runMotor(1, 200, true);
    }
    
    Vone = analogRead(potpin);
    
    currentSpeed = 1000;
    
    while (digitalRead(switchpin2) == LOW){
    runMotor(0, 200, true);
    runMotor(0, 200, true);
    }
    
    Vtwo = analogRead(potpin);
    
    Vall = Vone - Vtwo - 4;
    
    Vb= Vone -11;
    Vs= Vtwo +2;
    
   currentSpeed=400;
    
    for (x=1; x<1000; x++){
    runMotor(1, 200, true);
    runMotor(1, 200, true);
    runMotor(1, 200, true);
    runMotor(1, 200, true);
    }
  a=a+1;  
  }

  
  Signal=SumPsP/100;
  q=Signal;
  Move=SumPosP/100;
  
  RealSignal= (float)(Vs + (q-100.00)*(Vb-Vs)*(1.227e-3)); // 0.504, 100 and 4.502, 915
  
    if (y == 0){
    SignalChange= RealSignal;
    Change = 0;
    Upper = SignalChange + 3;
    Lower = SignalChange - 3;
    y=y+1;  
    }
  
  if (Change == 1){
    SignalChange = RealSignal;
    Change = 0;
    Upper = SignalChange + 3;
    Lower = SignalChange - 3;  
    }

  
  Upper = SignalChange + 3;
  Lower = SignalChange - 3;
  
     if (RealSignal > Upper || RealSignal < Lower){
        Change = 1;
        }
  
if (Change == 0){
  if(RealSignal - 2 >= Move){
    for(x=0; x<5; x++){
    runMotor(1, 200, true);
    runMotor(1, 200, true);
    runMotor(1, 200, true);
    runMotor(1, 200, true);
    }
  }
}

if (Change == 0){
  if(RealSignal + 2 <= Move){
    for(x=0; x<5; x++){
    runMotor(0, 200, true); 
    runMotor(0, 200, true);
    runMotor(0, 200, true); 
    runMotor(0, 200, true);
    }
  }
}
  
 if (Change == 0){
   if(RealSignal == Move){
    runMotor(1, 80, false);
    runMotor(1, 80, false);
    runMotor(1, 80, false);
    runMotor(1, 80, false);
 }
  }


if (Change == 1){
  if(SignalChange - 2 >= Move)
  {
    stopMotor(1, 900, true);
  }
}
  
if (Change == 1){
  if(SignalChange + 2 <= Move)
  {
    stopMotor(0, 900, true);
  }
}

if (Change == 1){
  if(SignalChange  == Move){
    runMotor(1, 900, false);
    runMotor(1, 900, false);
  }
}


    PosPP[i]=PosP;
    PsPP[i]=PsP;
    i++;
    
    if (i>=100){
     i=i-100;
     }


  //Serial.println(PsP);
  //Serial.println(PosP);
  //Serial.println(x);
  //Serial.println(i);
  //Serial.println(PosPP[3]);
  //Serial.println(PsP);
  //Serial.println(SumPosP);
  //Serial.println(SumPsP);
  //Serial.println(Move);
  //Serial.println(Signal);
  //Serial.println(digitalRead(switchpin1));
  //Serial.println(digitalRead(switchpin2));
  //Serial.println(Change);
  //Serial.println(currentSpeed);
  //Serial.println(Lower);
  //Serial.println(Upper);
  //Serial.println(Change);
  //Serial.println(SignalChange);
  //Serial.println(Select);
  //Serial.println(Vall);
  //Serial.println(Vb);
  //Serial.println(RealSignal);
  //Serial.println(q);


//%%%%%%%%%%%%%%%%%%%%%% OUT OF BOUNDS SWITCH PINS %%%%%%%%%%%%%%%%%%%%

/*
if (digitalRead(switchpin1) == HIGH){
  while (30 < Move){
    currentSpeed = 400;
    runMotor(0, 100, true);
    runMotor(0, 100, true);
    
    PosPP[i]=PosP;
    PsPP[i]=PsP;
    i++;
      
    PosP = analogRead(potpin);
    PsP = analogRead(PowSup);  
      
    Signal=SumPsP/100;
    Move=SumPosP/100;
    
    if (i>=100){
    i=i-100;
    }
  }
}

if (digitalRead(switchpin2) == HIGH){
  while (30 > Move){
    currentSpeed = 400;
    runMotor(1, 100, true);
    runMotor(1, 100, true);
    
    Serial.println(Move);
    
    PosPP[i]=PosP;
    PsPP[i]=PsP;
    i++;

    PosP = analogRead(potpin);
    PsP = analogRead(PowSup);

    Signal=SumPsP/100;
    Move=SumPosP/100;
    
    if (i>=100){
    i=i-100;
    }
}
}
*/


if (digitalRead(switchpin2) == HIGH){
  currentSpeed = 500;
  for (x=0; x<3000; x++){
  runMotor(1, 200, true);
  runMotor(1, 200, true);
  runMotor(1, 200, true);
  runMotor(1, 200, true);
  }
  currentSpeed = 400;
}

if (digitalRead(switchpin1) == HIGH){
    currentSpeed = 500;
  for (x=0; x<3000; x++){
  runMotor(0, 200, true);
  runMotor(0, 200, true);
  runMotor(0, 200, true);
  runMotor(0, 200, true); 
  } 
  currentSpeed = 400;
}
}

//%%%%%%%%%%%%%%%%% FUNTIONS %%%%%%%%%%%%%%%%%%%%%%%%%%

void runMotor(int dir, int speedNeeded, boolean on)
{
  if(on)
  {
    digitalWrite(onoffpin, HIGH);
    digitalWrite(dirpin, dir);
    digitalWrite(steppin, HIGH);
    delayMicroseconds(currentSpeed);
    digitalWrite(steppin, LOW);
    delayMicroseconds(currentSpeed);
      
    if(currentSpeed > speedNeeded)
    {
      currentSpeed -= 1;
    }
  } 
  
  else if(!on)
  {
    digitalWrite(onoffpin, LOW);
  }
}




void stopMotor(int dir, int speedNeeded, boolean on){
  if(on){
  while (currentSpeed < speedNeeded)
  {
    digitalWrite(onoffpin, HIGH);
    digitalWrite(dirpin, dir);
    digitalWrite(steppin, HIGH);
    delayMicroseconds(currentSpeed);
    digitalWrite(steppin, LOW);
    delayMicroseconds(currentSpeed);
      currentSpeed += 1;
   }
  }
  
  else if(!on)
  {
    digitalWrite(onoffpin, LOW);
  }
  SignalChange = Signal;
   if (i>=100){
  i=i-100;}
}



