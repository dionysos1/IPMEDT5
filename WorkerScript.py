import threading
from time import sleep
import time
import serial
import MySQLdb
import sys

# from timeit import default_timer as timer

usb_ports = [
    serial.Serial('/dev/ttyACM0', 9600),
    serial.Serial('/dev/ttyUSB0', 9600),
    serial.Serial('/dev/ttyUSB1', 9600)
]

display_text = 'test;test2'

db = MySQLdb.connect("localhost", "root", "fractal", "zelfservice")
db_cursor = db.cursor()

KG_string = 'kg'  # check string om te kijken of er gewicht wordt doorgestuurd
UID_string = 'UID'  # check string om te kijken of er een UID wordt doorgestuurd

active = True


def serial_read_product(usb):
    # lees data van de seriele verbinding
    if usb.in_waiting:
        incoming_string = usb.readline()

        split = incoming_string.split(' ')

        uid_and_weight_to_db(int(split[0]), float(split[1]))


def serial_read_product_id(read_serial, write_serial):
    # lees de data van de seriele verbinding
    if read_serial.in_waiting:
        uid = read_serial.readline()

        product = get_product_by_rfid(uid[:8])
        if not product:
            serial_write(write_serial, uid + "Ready...;Scan een product")
        if product:
            serial_write(write_serial, str(product[0]) + ";" + str(product[2]))
            # serial_write(write_serial, str(product[0]) + ";test")



def get_product_by_rfid(rfid):
    db_cursor.execute("SELECT name, rfid, price FROM products WHERE rfid = '{}'".format(rfid))
    results = db_cursor.fetchall()

    return results[0]


def serial_write(usb, args):
    # schrijf tekst naar het schermpje
    # tekst moet een ; bevatten voor het scherm om de regels te scheiden
    usb.write(args)


def uid_and_weight_to_db(uid, weight):
    # schrijf het huidige gewicht en uid naar de DB
    try:
        print("schrijf gewicht en uid naar database...")
        sys.stdout.flush()

        db_cursor.execute("UPDATE gebruiker_dump SET rfid = '{}', kg = '{}' WHERE id = 1".format(uid, weight))
        db.commit()
    except:
        print "Error: the database is being rolled back"
        db.rollback()


def product_read(usb):
    while True:

        serial_read_product(usb)
        time.sleep(.1)


def product_read_push_screen(read_usb, screen_usb):
    while True:
        serial_read_product_id(read_usb, screen_usb)
        time.sleep(.1)


def main():

    for usb in usb_ports:
        usb.flushInput()

    threads = [
        threading.Thread(target=product_read, args=(usb_ports[0])),
        threading.Thread(target=product_read_push_screen, args=(usb_ports[1], usb_ports[2])),
    ]

    print(get_product_by_rfid("0415bba2"))

    for thread in threads:
        thread.start()

    for thread in threads:
        thread.join()


if __name__ == '__main__':
    main()
