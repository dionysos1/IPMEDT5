#include <SPI.h>
#include <MFRC522.h>
#include "HX711.h" 

//RFID deel
constexpr uint8_t RST_PIN = 9;     // Configurable, see typical pin layout above
constexpr uint8_t SS_1_PIN = 10;   // Configurable, take a unused pin, only HIGH/LOW required, must be diffrent to SS 2
constexpr uint8_t SS_2_PIN = 8;    // Configurable, take a unused pin, only HIGH/LOW required, must be diffrent to SS 1

constexpr uint8_t NR_OF_READERS = 1;

byte ssPins[] = {SS_1_PIN, SS_2_PIN};

MFRC522 mfrc522[NR_OF_READERS];   // Create MFRC522 instance.

//loadcell deel
#define DOUT  3
#define CLK  2
#define DOUT2  5
#define CLK2  4

HX711 scale(DOUT, CLK);
HX711 scale2(DOUT2, CLK2);
int check = 0;
float calibration_factor = -96650; //-106600 worked for my 40Kg max scale setup 

char* last_UID;

void setup() { 
  //RFID
  Serial.begin(9600);
  SPI.begin();        // Init SPI bus
  for (uint8_t reader = 0; reader < NR_OF_READERS; reader++) {
    mfrc522[reader].PCD_Init(ssPins[reader], RST_PIN); // Init each MFRC522 card
    Serial.println();
  }

  //loadcell
  scale.set_scale();
  scale.tare(); //Reset the scale to 0
  scale2.set_scale();
  scale2.tare();
  long zero_factor = scale.read_average(); //Get a baseline reading

  Serial.println("starting gebruiker UNO");
  
   scale.set_scale(calibration_factor); //Adjust to this calibration factor
  scale2.set_scale(calibration_factor); //Adjust to this calibration factor
}
 
void loop() {
//=============================================================================================
//                         RFID
//=============================================================================================

  for (uint8_t reader = 0; reader < NR_OF_READERS; reader++) {
    // Look for new cards

    if (mfrc522[reader].PICC_IsNewCardPresent() && mfrc522[reader].PICC_ReadCardSerial()) {
      //dump_byte_array(mfrc522[reader].uid.uidByte, mfrc522[reader].uid.size);

      last_UID = uidBytesToHex(mfrc522[reader].uid.uidByte);

      // Halt PICC
      mfrc522[reader].PICC_HaltA();
      // Stop encryption on PCD
      mfrc522[reader].PCD_StopCrypto1();
    } //if (mfrc522[reader].PICC_IsNewC
  } //for(uint8_t reader

//=============================================================================================
//                         Loadcell
//=============================================================================================
 
  double one = (scale.get_units());
  double two = one + (scale2.get_units());
  if(two < -0.001)
    two = two + 0.002;
   else if(two > 0.002 && two < 0.015)
    two = two - 0.002;

    Serial.print(last_UID);
    Serial.print(" ");
    Serial.println(two);
    delay(500);
}

/**
 * Helper routine to dump a byte array as dec values to Serial.
 */
void printDec(byte *buffer, byte bufferSize) {
  for (byte i = 0; i < bufferSize; i++) {
    Serial.print(buffer[i] < 0x10 ? " 0" : ",");
    Serial.print(buffer[i], DEC);
  }
}
/*void dump_byte_array(byte *buffer, byte bufferSize) {

    last_UID = "";
  for (byte i = 0; i < bufferSize; i++) {
    last_UID += (buffer[i] < 0x10 ? "0" : "");
    last_UID += (buffer[i], HEX);
  }
}*/

void storeHexRepresentation(char *b, const byte v)
{
  if (v <= 0xF) {
    *b = '0';
    b++;
  }
  itoa(v, b, 16); // http://www.cplusplus.com/reference/cstdlib/itoa/
}

char* uidBytesToHex(byte *mfrc522_uid_uidByte) {
  char uidString[9];
  
  for (byte i = 0; i < 4; i++) storeHexRepresentation(&uidString[2 * i], mfrc522_uid_uidByte[i]);

  return uidString;
}

