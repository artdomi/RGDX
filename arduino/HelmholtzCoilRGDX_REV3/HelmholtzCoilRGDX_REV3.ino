int dirpin = 2;            // it's on a static pulse pin (direction input)
int steppin = 5;          // it's on a PWM~ pin (velocity of step input)
int onoffpin = 4;         // it's on a static pulse pin (on and off input)
int potpin = A0;
int userpin = A1;
int switchpin1 = 7;
int switchpin2 = 8;
int currentSpeed = 1000;
int finalSpeed = 200;
int wantedSpeed = 0;
long potPos = 0;
long userPos = 0;
long finalPos;
long userData[100];
long potData[100];
long potDataSum;
long userDataSum;
int runCount = 0;
boolean run = false;
boolean first = true;


void setup() {
  // put your setup code here, to run once:
  pinMode(dirpin, OUTPUT);     
  pinMode(steppin, OUTPUT);
  pinMode(onoffpin, OUTPUT);
  pinMode(switchpin1, INPUT);
  pinMode(switchpin2, INPUT);
  pinMode(potpin,INPUT);
  pinMode(userpin,INPUT);
  digitalWrite(dirpin, HIGH);
  digitalWrite(steppin, LOW);
  digitalWrite(onoffpin, HIGH);
  Serial.begin(9600);
}

void loop() {
  // put your main code here, to run repeatedly:
  if(first)
  {
    for(int i = 0; i < 100; i++)
    {
      potData[i] = (long) analogRead(potpin);
      userData[i] = (long) analogRead(userpin);
    }
    first = false;
  }

  if(runCount >= 100)
    runCount = 0;
    
  potData[runCount] = (long) analogRead(potpin);
  userData[runCount] = (long) analogRead(userpin);
  runCount++;

  potDataSum = 0;
  userDataSum = 0;
  for(int i = 0; i < 100; i++)
  {
    potDataSum += potData[i];
    userDataSum += userData[i];
  }
  
  potPos = potDataSum/100;
  userPos = map((userDataSum/100), 0, 1000, 50, 380);
  wantedSpeed = map((long)analogRead(A2), 0, 1023, 10, 800);

  if(userPos < potPos && run)
  {
    runMotor(HIGH, wantedSpeed, true);
  }

  if((userPos > potPos) && run)
  {
    runMotor(LOW, wantedSpeed, true);
  }

  /*if(potPos > userPos - 4 && potPos < userPos + 4)
  {
    runMotor(LOW, 200, false);
  }*/

  if(potPos == userPos)
  {
     runMotor(LOW, wantedSpeed, false);
     finalPos = userPos;
     run = false;
  }

  if(userPos < finalPos - 1 || userPos > finalPos + 1)
  {
    run = true;
  }

  if(digitalRead(switchpin1) == HIGH)
  {
    currentSpeed = 1000;
    for(int i = 0; i < 3000; i++)
    {
      runMotor(LOW, 500, true);
    }

    //finalPos = potPos;
    //run = false;
  }

  if(digitalRead(switchpin2)== HIGH)
  {
    currentSpeed = 1000;
    for(int i = 0; i < 3000; i++)
    {
      runMotor(HIGH, 500, true);
    }

    //finalPos = potPos;
    //run = false;
  }

 // Serial.println(potPos);
 // Serial.println(userPos);
 // Serial.println(analogRead(userpin));
 // Serial.println(userDataSum);
 // Serial.println(userData[runCount]);
  
  
}


//Functions
void runMotor(int dir, int speedNeeded, boolean on)
{
  if(on)
  {
    digitalWrite(onoffpin, HIGH);
    digitalWrite(dirpin, dir);
    digitalWrite(steppin, HIGH);
    delayMicroseconds(speedNeeded);
    digitalWrite(steppin, LOW);
    delayMicroseconds(speedNeeded);
      
    /*if(currentSpeed > speedNeeded)
    {
      currentSpeed -= 1;
    }*/
  } 
  
  else if(!on)
  {
    digitalWrite(onoffpin, LOW);
  }
}





