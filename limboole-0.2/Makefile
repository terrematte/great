VERSION="\"`cat VERSION`\""
CC=gcc

CFLAGS=-DVERSION=$(VERSION) -Wall -g -fPIE
LDFLAGS=-pie

all: limboole testlimboole dimacs2boole

limboole: main.o limboole.o ../limmat/liblimmat.a
	$(CC) $(CFLAGS) $(LDFLAGS) -o $@ main.o limboole.o -L../limmat -llimmat

testlimboole: test.o limboole.o ../limmat/liblimmat.a
	$(CC) $(CFLAGS) $(LDFLAGS) -o $@ test.o limboole.o -L../limmat -llimmat

dimacs2boole: dimacs2boole.c
	$(CC) $(CFLAGS) -o $@ dimacs2boole.c

limboole.o: limboole.c
	$(CC) $(CFLAGS) -c -I../limmat limboole.c

test.o: test.c
	$(CC) $(CFLAGS) -c test.c

main.o: main.c
	$(CC) $(CFLAGS) -c main.c

clean:
	rm -f limboole testlimboole dimacs2boole
	rm -f *.o *.a
	rm -f log/*.log
	rm -f *.bb *.bbg *.gcov *.da
