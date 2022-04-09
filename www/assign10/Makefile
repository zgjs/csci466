# CSCI 466 - Assignment 10 - MariaDB from C++
# zgjs <zgjs@zgjs.dev>
# Due 2020-11-20

# Make variables
CXX      := g++
CXXFLAGS := -std=c++11 -Wall -Wextra -I/usr/include/mariadb -I/usr/include/mariadb/server/
LDLIBS   := -lmariadb

# Make rules
all: books
# The following rules match GNU Make implicit rules, so they are actually unnecessary.
books: books.o
	$(CXX) $^ $(LDLIBS) -o $@

%.o: %.cc
	$(CXX) $(CXXFLAGS) -c $< -o $@

.PHONY: clean
clean:
	-rm books *.o
