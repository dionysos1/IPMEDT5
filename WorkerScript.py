import time
import serial
import MySQLdb

# initialiseer de usb devices
# ACM0 is een arduino UNO met de gewichtssensor en RFID lezer.
# USB0 is een arduino NANO met RFID lezer.
# USB1 is een arduino NANO met een LCD scherm eraan.

usb_ports = [
    serial.Serial('/dev/ttyACM0', 9600),
    serial.Serial('/dev/ttyUSB0', 9600),
    serial.Serial('/dev/ttyUSB1', 9600)
]

# initialiseer de connectie met de database
db = MySQLdb.connect("localhost", "root", "fractal", "zelfservice")
db_cursor = db.cursor()


def serial_read_product(usb):
    # lees data van de seriele verbinding
    if usb.in_waiting:
        incoming_string = usb.readline()
        print(incoming_string)
        split = incoming_string.split(' ')
        if len(split) < 2:
            return
        uid_and_weight_to_db(str(split[0]), split[1])


def serial_read_product_id(read_serial, write_serial):
    # lees de data van de seriele verbinding
    if read_serial.in_waiting:
        uid = read_serial.readline()
        # vraag aan db wat deze tag is en kost.
        product = get_product_by_rfid(uid[:8])
        if not product:
            serial_write(write_serial, uid + "Ready...;Scan een product")
        if product:
            serial_write(write_serial, str(product[0]) + ";" + str(product[2]))


def get_product_by_rfid(rfid):
    # vraag aan de db wat de naam en kosten zijn van de opgegeven tag.
    db_cursor.execute("SELECT name, rfid, price FROM product_status WHERE rfid = '{}'".format(rfid))

    results = db_cursor.fetchall()
    # als het product niet bekend is geef dan aan dat het product onbekend is.
    if len(results) == 0:
        return "UNKNOWN PRODUCT", "UNKNOWN PRODUCT", "UNKNOWN PRODUCT"
    else:
        return results[0]


def serial_write(usb, args):
    # schrijf tekst naar het schermpje
    # tekst moet een ; bevatten voor het scherm om de regels te scheiden
    usb.write(args)


def uid_and_weight_to_db(uid, weight):
    # schrijf het huidige gewicht en uid naar de DB
    db_cursor.execute("UPDATE gebruiker_dump SET rfid = '{}', kg = '{}' WHERE id = 1".format(uid, weight))
    time.sleep(.1)
    db.commit()


def main():
    for usb in usb_ports:
        usb.flushInput()

    time.sleep(2)
    while True:
        serial_read_product(usb_ports[0])
        serial_read_product_id(usb_ports[1], usb_ports[2])
        time.sleep(.1)


if __name__ == '__main__':
    main()
