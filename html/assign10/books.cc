/**
 * CSCI 466 - Assignment 10 - MariaDB from C++
 * zgjs <zgjs@zgjs.dev>
 * Due 2020-11-20
 */

// Database settings
#define HOST "courses"
#define USER "anonymous"
#define PASSWORD NULL
#define DBNAME "henrybooks"
#define DEBUG 1

// Includes
#include <iostream> // cin, cout
//#include <iomanip> // setw setfill
//#include <curses.h> // ncurses
//#include <menu.h> // ncurses
#include <mysql.h> // mysql_*
#include <stdio.h> // fprintf
#include <stdlib.h> // exit
#include <string.h> // strlen
#include <string> // string

/**
 * prompt the user for an input, given a choice, repeating the question until they answer
 * 
 * @param prompt the message
 * @return a positive integer with the answer to the prompt
 */
int prompt_int(const char prompt[]) {
    int input = -1;
    while(input < 0) {
        std::cout << prompt;
        std::cin >> input;
        if(std::cin.fail()) {
            std::cin.clear();
            std::cin.ignore(1024, '\n');
        }
    }
    return input;
}
/**
 * prompt the user for an input, given a choice, repeating the question until they answer
 * 
 * @param prompt the message
 * @return a std::string with the answer to the prompt
 */
std::string prompt_str(const char prompt[]) {
    std::string input;
    do {
        // Reset the stream state and clear the cin buffer
        std::cin.clear();
        std::cin.ignore(1024, '\n');
        std::cout << prompt;  // Prompt the user
        getline(std::cin, input); // Get the input
    } while (std::cin.fail());
    return input;
}
/**
 * Wait for the user to press enter
 * 
 * @param preclear Should the cin buffer be cleared first?
 */
void pause_enter(bool preclear = false) {
    if(preclear) {
        std::cin.clear();
        std::cin.ignore(1024,'\n');
    }
    std::cout << "Press enter to continue..." << std::flush;
    std::cin.get();
    if(std::cin.fail()) {
        std::cin.clear();
        std::cin.ignore(1024,'\n');
    }
}
/**
 * Run a mysql query and return the result
 * 
 * @param mysql a MySQL connection handler
 * @param sql the query string
 * @return the result of the query (0 if failed)
 */
MYSQL_RES* run_query(MYSQL *mysql, const char sql[]) {
    if (mysql_query(mysql, sql)) {
        fprintf(stderr, "%s\n", mysql_error(mysql));
        mysql_close(mysql);
        mysql_library_end();
        exit(EXIT_FAILURE);
    }
    MYSQL_RES *result = mysql_store_result(mysql);
    if (result == NULL)
    {
        mysql_close(mysql);
        mysql_library_end();
        exit(EXIT_FAILURE);
    }
    return result;
}
/**
 * Print out a mysql result in a very generic way
 * 
 * @param result a MYSQL_RES object
 */
void print_result(MYSQL_RES *result) {
    size_t num_fields = mysql_num_fields(result);
    MYSQL_ROW row;
    while ((row = mysql_fetch_row(result))) {
        for(size_t i = 0; i < num_fields; i++) {
            printf("%s\t", row[i] ?: "NULL");
        }
        printf("\n");
    }
}
//void print_result_table(MYSQL_RES *result, const char *headings[], const int padding[]);
/**
 * Print a MYSQL_RES as a table with box drawing characters
 * 
 * @param result the MYSQL_RES instance
 * @param padding an array of integers of how much to padd each column
 */
void print_result_table(MYSQL_RES *result, const size_t padding[]) {
    int num_fields = mysql_num_fields(result);
    MYSQL_ROW row;
    while ((row = mysql_fetch_row(result))) {
        for(int i = 0; i < num_fields; i++) {
            //printf("%s ", row[i] ?: "NULL");
            //printf("║ %s%*.*s ", row[i] ?: "NULL", padding[i], padding[i], " ");
            printf(
                "║ %s%*s",
                row[i] ?: "NULL",
                (int) ((padding[i] < strlen(row[i] ?: "NULL")) ? 0 : padding[i] - strlen(row[i] ?: "NULL")),
                " "
            );
        }
        printf("║\n");
    }
}
/**
 * This just runs run_query() and feeds the results to print_result()
 * @param mysql a MYSQL connection handler
 * @param sql the query string
 */
void print_query(MYSQL *mysql, const char sql[]) {
    print_result(run_query(mysql, sql));
}
/**
 * Print book search results
 * 
 * @param result a MYSQL_RES instance
 */
void print_book_search_results(MYSQL *mysql, MYSQL_RES *result) {
    const size_t padding[] = {10, 41, 22, 6};
    size_t num_fields = 4; //mysql_num_fields(result);
    MYSQL_ROW row;
    printf(R"DEL(
╔═══════════╦══════════════════════════════════════════╦═══════════════════════╦═══════╗
║ Book Code ║ Title                                    ║ Author                ║ Price ║
╠═══════════╬══════════════════════════════════════════╬═══════════════════════╬═══════╣
)DEL");
    size_t num_rows = mysql_num_rows(result);
    size_t current_row = 0;
    while ((row = mysql_fetch_row(result))) {
        current_row++;
        for(size_t i = 0; i < num_fields; i++) {
            printf(
                "║ %s%*s",
                row[i] ?: "NULL",
                (int) ((padding[i] < strlen(row[i] ?: "NULL")) ? 0 : padding[i] - strlen(row[i] ?: "NULL")),
                " "
            );
        }
        printf("║\n");

        // Sub query for each bookcode
        char branch_query[1024];
        sprintf(branch_query,
            "SELECT BranchName, BranchLocation, OnHand "
            "FROM Inventory "
            "INNER JOIN Branch USING (BranchNum) "
            "INNER JOIN Book USING (BookCode) "
            "WHERE BookCode = '%s'", row[0]);
        //printf("Query: %s\n", branch_query);
        MYSQL_RES *branch_result = run_query(mysql, branch_query);
        size_t num_branch_rows = mysql_num_rows(branch_result);
        if(num_branch_rows == 0) {
            printf("║           ╚══════════════════════════════════════════╩═══════════════════════╩═══════╣\n");
            printf("║           This book is out of stock everywhere                                       ║\n");
            if(current_row != num_rows) {
            printf("╠═══════════╦══════════════════════════════════════════╦═══════════════════════╦═══════╣\n");
            } else {
            printf("╚══════════════════════════════════════════════════════════════════════════════════════╝\n");
            }
        } else {
            printf("║           ╚═════════╦════════════════════════════════╬═══════════════════════╬═══════╣\n");
            printf("║           Inventory ║ Branch Name                    ║ Branch Location       ║ Stock ║\n");
            printf("║                     ╠════════════════════════════════╬═══════════════════════╬═══════╣\n");
          //printf("║                     ║ Branch Name                    ║ Branch Location       ║ Stock ║\n");
            MYSQL_ROW branch_row;
            while ((branch_row = mysql_fetch_row(branch_result))) {
            printf("║                     ║ %-30s ║ %-21s ║ %5s ║\n", branch_row[0], branch_row[1], branch_row[2]);
            }
            if(current_row != num_rows) {
            printf("╠═══════════╦═════════╩════════════════════════════════╬═══════════════════════╬═══════╣\n");
            } else {
            printf("╚═════════════════════╩════════════════════════════════╩═══════════════════════╩═══════╝\n");
            }
        }
    }
    //printf(    "╚══════════════════════════════════════════════════════════════════════════════════════╝\n");
}
/**
 * Book list
 * 
 * @param mysql A MYSQL connection handler
 */
void book_list(MYSQL *mysql) {
    const size_t padding[3] = {41,22,6};
    const char sql[] = 
        "SELECT Title, CONCAT(AuthorFirst, ' ', AuthorLast) AS Author, Price AS Cost "
        "FROM Wrote "
        "INNER JOIN Book USING (BookCode) "
        "INNER JOIN Author USING (AuthorNum) "
        "ORDER BY Title,Sequence";
    printf(R"DEL(
╔══════════════════════════════════════════╦═══════════════════════╦═══════╗
║ Title                                    ║ Author                ║ Cost  ║
╠══════════════════════════════════════════╬═══════════════════════╬═══════╣
)DEL");
    //print_result_table(run_query(&mysql, sql), headings, padding);
    print_result_table(run_query(mysql, sql), padding);
    printf("╚══════════════════════════════════════════╩═══════════════════════╩═══════╝\n");
    pause_enter(true);
}
/**
 * Author search
 * 
 * @param mysql A MYSQL connection handler
 */
void author_search(MYSQL *mysql) {
    printf(R"DEL(
    ╔═══════════════════════════════════════════════════════════╗
    ║                       Author Search                       ║
    ║ You may search by all or part of an author's name.        ║
    ║ Results for partial matches will be returned.             ║
    ║ Please enter the author's first and last name separately. ║
    ║ Hit enter when done typing each name.                     ║
    ╚═══════════════════════════════════════════════════════════╝
)DEL");
    std::string author_first;
    std::string author_last;
    std::cin.clear();
    std::cin.ignore(1024,'\n');
    bool read = false;
    while(!read) {
        std::cout << "Author's first name: ";
        std::getline(std::cin, author_first);
        std::cout << "Author's last  name: ";
        std::getline(std::cin, author_last);
        if (author_first.empty() && author_last.empty()) {
            std::cout << "Please enter at least the first or last name" << std::endl;
        } else {
            read = true;
        }
    }
    char query[1024], *end;
    end = stpcpy(query,
        "SELECT BookCode, Title, CONCAT(AuthorFirst,' ',AuthorLast) AS Author, Price "
        "FROM Wrote "
        "INNER JOIN Author USING (AuthorNum) "
        "INNER JOIN Book USING (BookCode) "
        "WHERE "
    );
    if(!author_first.empty()) {
        end  = stpcpy(end,"AuthorFirst LIKE '%");
        end += mysql_real_escape_string(mysql, end, author_first.c_str(), author_first.length());
        end  = stpcpy(end,"%'");
    }
    if(!author_first.empty() && !author_last.empty()) {
        end  = stpcpy(end," OR ");
    }
    if(!author_last.empty()) {
        end  = stpcpy(end,"AuthorLast LIKE '%");
        end += mysql_real_escape_string(mysql, end, author_last.c_str(), author_last.length());
        end  = stpcpy(end,"%'");
    }
    //printf("Query: %s\n", query);
    MYSQL_RES *result = run_query(mysql, query);
    size_t num_rows = mysql_num_rows(result);
    if(num_rows == 0) {
        std::cout << "Sorry, no results for " << author_first << " " << author_last << std::endl;
        pause_enter();
        return;
    }
    print_book_search_results(mysql, result);
    pause_enter();
}
/**
 * Title search
 * 
 * @param mysql A MYSQL connection handler
 */
void title_search(MYSQL *mysql) {

    printf(R"DEL(
    ╔══════════════════════════════════════════════════╗
    ║                  Title Search                    ║
    ║ You may search by all or part of the book title. ║
    ║ Results for partial matches will be returned.    ║
    ╚══════════════════════════════════════════════════╝
)DEL");
    std::string title;
    std::cin.clear();
    std::cin.ignore(1024,'\n');
    bool read = false;
    while(!read) {
        std::cout << "Title: ";
        std::getline(std::cin, title);
        if (title.empty()) {
            std::cout << "Please enter at least part of the title." << std::endl;
        } else {
            read = true;
        }
    }
    char query[1024], *end;
    end = stpcpy(query,
        "SELECT BookCode, Title, CONCAT(AuthorFirst,' ',AuthorLast) AS Author, Price "
        "FROM Wrote "
        "INNER JOIN Author USING (AuthorNum) "
        "INNER JOIN Book USING (BookCode) "
        "WHERE Sequence = '1' AND TITLE LIKE '%"
    );
    end += mysql_real_escape_string(mysql, end, title.c_str(), title.length());
    end  = stpcpy(end,"%'");
    //printf("Query: %s\n", query);
    MYSQL_RES *result = run_query(mysql, query);
    size_t num_rows = mysql_num_rows(result);
    if(num_rows == 0) {
        std::cout << "Sorry, no results for " << title << "." << std::endl;
        pause_enter();
        return;
    }
    print_book_search_results(mysql, result);
    pause_enter();
}
/**
 * Main
 */
int main() {
    // Based on example from https://dev.mysql.com/doc/c-api/5.7/en/mysql-library-init.html
    // This function would actually be called automatically by mysql_init(), but it can be called explicitly as well.
    // According to the docs, if we are not running embedded server, the optional arguments are not useful,
    // so mysql argc, *argv[], and *groups[] are all 0 or NULL.
    if (mysql_library_init(0, NULL, NULL)) { 
        fprintf(stderr, "could not initialize MySQL client library\n");
        exit(EXIT_FAILURE);
    }
    // Initialize MYSQL connection object.
    // I know a the exampples show declaring the MYSQL object as a pointer to an instance on the heap,
    // but the official MySQL documentation shows that it's fine to allocate on the stack,
    // as long as I don't try to make a copy of it.
    // There are some private data members that will end up on the heap anyway.
    MYSQL mysql;
    if (!mysql_init(&mysql))
    {
        fprintf(stderr, "Failed to initialize mysql: Error: %s\n", mysql_error(&mysql));
        mysql_library_end();
        exit(EXIT_FAILURE);
    }
    // Connect to the database
    if (!mysql_real_connect(&mysql,HOST,USER,PASSWORD,DBNAME,0,NULL,0))
    {
        fprintf(stderr, "Failed to connect to database: Error: %s\n", mysql_error(&mysql));
        mysql_library_end();
        exit(EXIT_FAILURE);
    }
    // Databases is connected successfully
    int choice;
    while ((choice = prompt_int(R"DEL(
    ╔════════════════════════════╗
    ║   Welcome to Henrybooks!   ║
    ║  Please make a selection:  ║
    ║   ╭────────────────────╮   ║
    ║   │  1. Book List      │   ║
    ║   │  2. Author Search  │   ║
    ║   │  3. Title Search   │   ║
    ║   │  4. Quit           │   ║
    ║   ╰────────────────────╯   ║
    ║ What would you like to do? ║
    ╚════════════════════════════╝
    Your choice: )DEL")) != 4) {
        switch(choice) {
        case 1:
            {
                //printf("You selected book list\n");
                book_list(&mysql);
                break;
            }
        case 2:
            {
                //printf("You selected author search\n");
                author_search(&mysql);
                break;
            }
        case 3:
            {
                //printf("You selected title search\n");
                title_search(&mysql);
                break;
            }
        case 4:
            {
                printf("Goodbye\n");
                break;
            }
        default:
            {
                printf("That option (%d) is not valid. Please try again.\n", choice);
                break;
            }
        }
    }
    mysql_close(&mysql);
    mysql_library_end();
    return EXIT_SUCCESS;
}
