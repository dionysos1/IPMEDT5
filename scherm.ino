#include <LiquidCrystal.h>
const int rs = 12, en = 11, d4 = 5, d5 = 4, d6 = 3, d7 = 2;
LiquidCrystal lcd(rs, en, d4, d5, d6, d7);
String zin = "hoi";
String row1, row2;

void setup() {
  lcd.begin(16, 2);
  Serial.begin(9600);
}

void loop() {
  if (Serial.available()) {
    lcd.clear();
    zin = Serial.readString();

    for (int i = 0; i < zin.length(); i++) {
      if (zin.substring(i, i+1) == ";") {
        row1 = zin.substring(0, i);
        row2 = zin.substring(i+1);
        break;
      }
    }

  lcd.setCursor(0,0);
  lcd.print(row1);
  // Serial.println(row1);
  lcd.setCursor(0,1);
  lcd.print(row2);
  // Serial.println(row2);
  }
}
