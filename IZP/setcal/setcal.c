#include <stdlib.h>
#include <stdio.h>
#include <string.h>

/// GENERAL STRING WORK

/**
 * Counts the number of spaces in a string. Sequence of several spaces counts as 1
 * @param input input string
 * @return number of spaces in a string
 */
int space_count(const char* input) {
    int count = 0;
    for (int i = 0; i < strlen(input) - 1; i++) {
        // handle cases with multiple spaces
        if (input[i] == ' ' && ((i == strlen(input) - 1) | (input[i + 1] != ' '))) {
            count++;
        }
    }
    return count;
}

/**
 * splits an input string into words with spaces
 * @param input input string
 * @param word_count number of spaces or sequences of spaces
 * @return array of substrings
 */
char** split_to_words(const char* input, int word_count) {
    char **word_array = malloc(word_count * sizeof(char*));
    for (int i = 0; i < word_count; i++) {
        word_array[i] = calloc(30, sizeof(char));
    }
    int word_index = 0;
    int char_index = 0;
    for (int i = 2; i < strlen(input) && input[i] != '\n'; i++) { //starting with the third symbol, skipping type identifier and the first space.
        if (input[i] == ' ') {
            //handle cases with multiple spaces
            if (input[i - 1] == ' ') {
                continue;
            }
            word_index++;
            char_index = 0;
        } else {
            word_array[word_index][char_index++] = input[i];
        }
    }


    return word_array;
}

/**
 * Converts a string to a number if possible
 * @param num_str input string
 * @return number
 */
int to_num(char* num_str) {
    return (int) strtol(num_str, (char **)NULL, 10);
}

/**
 * Prints false, if 0 is returned, else true
 * @param arg 1 or 0
 */
void printBool(int arg) {
    if (arg != 0) {
        printf("true\n");
    } else {
        printf("false\n");
    }
}

///// SET FUNCTIONS

typedef struct {
    char** items;
    int size;
    int is_universe;
} Set;

/**
 * @return empty set
 */
Set empty_set() {
    static Set set;
    set.size = 0;
    return set;
}

/**
 * Creates set of array of elements
 * @param items array of elements
 * @param size size of the set
 * @return set
 */
Set *from_items(char** items, int size) {
    Set *set = malloc(sizeof(Set));
    set->items = items;
    set->size = size;
    set->is_universe = 0;
    return set;
}

/**
 * compares two sets (the order may be different)
 * @param a the first set
 * @param b the second set
 * @return 1 if sets are equal, else 0
 */
int equals(Set *a, Set *b) {
    if (a->size != b->size) {
        return 0;
    }
    for (int i = 0; i < a->size; i++) {
        int count = 0;
        for (int j = 0; j < b->size; j++) {
            if (!strcmp(a->items[i], b->items[j])) {
                count++;
            }
        }
        if (count != 1) {
            return 0;
        }
    }
    return 1;
}

/**
 * frees the memory occupied by the elements of the set and the memory of the pointer to the set
 * @param set set to clear
 */
void free_set(Set *set) {
    if (set == NULL) {
        return;
    }
    for (int i = 0; i < set->size && set->items != NULL; i++) {
        free(set->items[i]);
    }
    if (set->items != NULL) {
        free(set->items);
    }
    free(set);
}

/**
 * Intersection function
 * @param a first set
 * @param b second set
 * @return set - intersection of sets a and b
 */
Set *intersect(Set *a, Set *b) {
    int intersect_size = 0;
    for (int j = 0; j < b->size; j++) {
        for (int i = 0; i < a->size; i++) {
            if (strcmp(b->items[j], a->items[i]) == 0) {
                intersect_size++;
            }
        }
    }

    if (intersect_size == 0) {
        Set *e = malloc(sizeof(Set));
        *e = empty_set();
        return e;
    }
    char** array = malloc(intersect_size * sizeof(char*));

    int index = 0;
    for (int j = 0; j < b->size; j++) {
        for (int i = 0; i < a->size; i++) {
            if (strcmp(b->items[j], a->items[i]) == 0) {
                array[index++] = strdup(b->items[j]);
            }
        }
    }

    return from_items(array, index);
}

/**
 * Determines if a is a subset of b or equals to b
 * @param a first set
 * @param b second set
 * @return result
 */
int subseteq(Set *a, Set *b) {
    Set *i = intersect(a, b);
    int res = equals(a, i);
    free_set(i);
    return res;
}

/**
 * Reads set. Checks if there are duplicates in the set and if the set belongs to universe.
 * @param input input string
 * @param universe
 * @return set
 */
Set* parse_set(char* input, Set *universe) {
    int count = space_count(input);
    if (count == 0) {
        Set *e = malloc(sizeof(Set));
        *e = empty_set();
        return e;
    }
    char** items = split_to_words(input, count);
    Set* set = from_items(items, count);


    for (int i = 0; i < set->size; i++) {
        for (int j = 0; j < set->size; j++) {
            if (i != j && set->items[i] == set->items[j]) {
                fprintf(stderr, "There are duplicates in the set");
                exit(EXIT_FAILURE);
            }
        }
    }

    if (universe != NULL && !subseteq(set, universe)) {
        fprintf(stderr, "Set is not part of the universe");
        exit(EXIT_FAILURE);
    }
    return set;
}

/**
 * Reads universe
 * @param input input string
 * @return universe
 */
Set *parse_universe(char *input) {
    Set *universe = parse_set(input, NULL);
    universe->is_universe = 1;
    return universe;
}

/**
 * outputs set to stdout
 * @param set set
 */
void print_set(Set *set) {
    if (set->is_universe) {
        printf("U ");
    } else {
        printf("S ");
    }
    for (int i = 0; i < set->size; i++) {
        printf("%s ", set->items[i]);
    }
    printf("\n");
}

/**
 * ОChecks if the set is empty
 * @param set set to check
 * @return 1 if the set is empty, else 0
 */
int empty(Set *set) {
    Set empty = empty_set();
    int res = equals(set, &empty);
    return res;
}

/**
 * returns size of the set
 * @param set
 * @return size
 */
int card(Set *set) {
    return set->size;
}

/**
 * function of union of sets a and b
 * @param a first set
 * @param b second set
 * @return result of the union
 */
Set* set_union(Set *a, Set *b) {
    int union_size = a->size;
    for (int j = 0; j < b->size; j++) {
        int is_in_a = 0;
        for (int i = 0; i < a->size; i++) {
            if (strcmp(b->items[j], a->items[i]) == 0) {
                is_in_a = 1;
            }
        }
        if (!is_in_a) {
            union_size++;
        }
    }

    char** array = malloc(union_size * sizeof(char*));
    for (int i = 0; i < a->size; i++) {
        array[i] = strdup(a->items[i]);
    }
    int index = a->size;
    for (int j = 0; j < b->size; j++) {
        int is_in_a = 0;
        for (int i = 0; i < a->size; i++) {
            if (strcmp(b->items[j], a->items[i]) == 0) {
                is_in_a = 1;
            }
        }
        if (!is_in_a) {
            array[index++] = strdup(b->items[j]);
        }
    }
    return from_items(array, index);
}

/**
 * counts set a minus set b
 * @param a first set
 * @param b second set
 * @return set of element a, which do not belong to b
 */
Set *minus(Set *a, Set *b) {
    int subtract_size = a->size;
    for (int j = 0; j < b->size; j++) {
        for (int i = 0; i < a->size; i++) {
            if (strcmp(b->items[j], a->items[i]) == 0) {
                subtract_size--;
            }
        }
    }

    if (subtract_size == 0) {
        Set *e = malloc(sizeof(Set));
        *e = empty_set();
        return e;
    }

    char** array = malloc(subtract_size * sizeof(char*));

    int index = 0;
    for (int i = 0; i < a->size; i++) {
        int is_not_in_b = 1;
        for (int j = 0; j < b->s
        ize; j++) {
            if (strcmp(b->items[j], a->items[i]) == 0) {
                is_not_in_b = 0;
            }
        }
        if (is_not_in_b) {
            array[index++] = strdup(a->items[i]);
        }
    }

    return from_items(array, index);
}

/**
 * counts complement
 * @param set set
 * @param universe universe
 * @return  set of elements, which are in universe, but are not in the initial set
 */
Set *complement(Set *set, Set *universe) {
    return minus(universe, set);
}

/**
 * Determines if a is a subset of b (a is not equal to b)
 * @param a first set
 * @param b second set
 * @return 1 if a is a subset, else 0
 */
int subset(Set *a, Set *b) {
    return subseteq(a, b) && !equals(a, b);
}

typedef struct {
    char* from_el;
    char* to_el;
} Pair;

typedef struct {
    Pair *pairs;
    int size;
} Relation;


/**
 * compare pairs
 * @param p1
 * @param p2
 * @return 1 if elements are equal, else 0
 */
int pairs_equal(Pair p1, Pair p2) {
    return !strcmp(p1.from_el, p2.from_el) && !strcmp(p1.to_el, p2.to_el);
}

/**
 * Reads the first value of a pair from a string
 * @param word input string representation of the first element
 * @return the first element of a pair without a parenthesis
 */
char *parse_first_pair_item(char *word) {
    if (word[0] != '(') {
        fprintf(stderr, "Incorrect pair syntax");
        exit(EXIT_FAILURE);
    }
    char *first = calloc(strlen(word) - 1, sizeof(char));
    for (int i = 1; i < strlen(word); i++) {
        first[i - 1] = word[i];
    }
    return first;
}

/**
 * Reads the second value of a pair from a string
 * @param word input string representation of the second element
 * @return the second element of a pair without a parenthesis
 */
char *parse_second_pair_item(char *word) {
    if (word[strlen(word) - 1] != ')') {
        fprintf(stderr, "Incorrect pair syntax");
        exit(EXIT_FAILURE);
    }
    char *second = calloc((strlen(word) - 1), sizeof(char));
    for (int i = 0; i < strlen(word) - 1; i++) {
        second[i] = word[i];
    }
    return second;
}

/**
 * Creates an array of pairs to display from the input string by the number of spaces
 * @param input input string
 * @param count number of spaces (including between elements inside brackets)
 * @return element pairs
 */
Pair *pairs_from_items(char **input, int count) {
    Pair *pairs = malloc(count / 2 * sizeof(Pair));
    for (int i = 0; i < count; i+= 2) {
        Pair pair;
        pair.from_el = parse_first_pair_item(input[i]);
        pair.to_el = parse_second_pair_item(input[i + 1]);
        pairs[i / 2] = pair;
    }
    return pairs;
}

/**
 * Determines if a set of values contains mapping pairs
 * @param set set
 * @param relation mapping for validation
 * @return 1 if all relation elements are present in the set
 */
int set_contains(Set *set, Relation *relation) {
    for (int i = 0; i < relation->size; i++) {
        int set_contains = 0;
        for (int j = 0; j < set->size; j++) {
            if (!strcmp(set->items[j], relation->pairs[i].to_el) ||
                !strcmp(set->items[j], relation->pairs[i].from_el)) {
                set_contains = 1;
                break;
            }
        }
        if (!set_contains) {
            return 0;
        }
    }
    return 1;
}

/**
 * Creates a relation on an input string. Checks for duplicate pairs and items in the universe
 * @param input input string
 * @param universe universe
 * @return ready relation
 */
Relation *parse_relation(char* input, Set* universe) {
    int count = space_count(input);
    if (count == 0 || count % 2 != 0) {
        fprintf(stderr, "Relation couldn't be initialized");
        exit(EXIT_FAILURE);
    }
    char** elements = split_to_words(input, count);
    Pair *pairs = pairs_from_items(elements, count);

    for (int i = 0; i < count; i++) {
        free(elements[i]);
    }
    free(elements);

    for (int i = 0; i < count / 2; i++) {
        for (int j = 0; j < count / 2; j++) {
            if (i != j && pairs_equal(pairs[i], pairs[j])) {
                fprintf(stderr, "There are duplicates in the relation");
                exit(EXIT_FAILURE);
            }
        }
    }

    Relation *r = malloc(sizeof(Relation));
    r->pairs = pairs;
    r->size = count / 2;

    if (universe != NULL && !set_contains(universe, r)) {
        fprintf(stderr, "Relation items is not part of the universe");
        exit(EXIT_FAILURE);
    }

    return r;
}

/**
 * Frees relation memory and pointer
 * @param relation pointer to relation
 */
void free_relation(Relation *relation) {
    if (relation == NULL) {
        return;
    }
    for (int i = 0; i < relation->size && relation->pairs != NULL; i++) {
        free(relation->pairs[i].from_el);
        free(relation->pairs[i].to_el);
    }
    if (relation->pairs != NULL) {
        free(relation->pairs);
    }
    free(relation);
}

/**
 * Print the display to stdout
 * @param relation relation
 */
void print_relation(Relation *relation) {
    printf("R ");
    for (int i = 0; i < relation->size; i++) {
        printf("(%s ", relation->pairs[i].from_el);
        printf("%s) ", relation->pairs[i].to_el);
    }
    printf("\n");
}

/**
 * Determines the occurrence of a string in an array of a given size
  * @param arr array
  * @param str string to check
  * @param size size of the array
  * @return 1 if the string is contained in the array at least once, 0 if not
 */
int contains(char **arr, char* str, int size) {
    for (int i = 0; i < size; i++) {
        if (!strcmp(arr[i], str)) {
            return 1;
        }
    }
    return 0;
}

/**
 * Build domain set of relation. These are the first elements of the relation pairs
  * @param relation relation
  * @return domain set
 */
Set *domain(Relation *relation) {
    char **elements = malloc(sizeof(char*) * relation->size);
    int cur_index = 0;
    for (int i = 0; i < relation->size; i++) {
        if (!contains(elements, relation->pairs[i].from_el, cur_index)) {
            elements[cur_index++] = strdup(relation->pairs[i].from_el);
        }
    }
    elements = realloc(elements, sizeof(char*) * cur_index);
    Set *d = from_items(elements, cur_index);
    return d;
}

/**
 * Builds a codomain (second elements of relation pairs)
 * @param relation relation
 * @return codomain
 */
Set *codomain(Relation *relation) {
    char **elements = malloc(sizeof(char*) * relation->size);
    int cur_index = 0;
    for (int i = 0; i < relation->size; i++) {
        if (!contains(elements, relation->pairs[i].to_el, cur_index)) {
            elements[cur_index++] = strdup(relation->pairs[i].to_el);
        }
    }
    elements = realloc(elements, sizeof(char*) * cur_index);
    Set *d = from_items(elements, cur_index);
    return d;
}

/**
 * Determines if the relation is reflective. For each x from the domain there must be a relation to x
 * @param relation relation
 * @return 1 if the relation is reflexive, else 0
 */
int reflexive(Relation *relation) {
    Set *d = domain(relation);
    int reflex_count = 0, domain_size = d->size;
    for (int i = 0; i < d->size; i++) {
        int has_reflex = 0;
        for (int j = 0; j < relation->size && !has_reflex; j++) {
            if (!strcmp(d->items[i], relation->pairs[j].to_el)) {
                reflex_count ++;
                has_reflex = 1;
            }
        }
    }
    free_set(d);
    return reflex_count == domain_size;
}

/**
 * Checks if the relation is symmetrical. For each x, y it is true that there is a pair (x y) and (y x)
 * @param relation relation
 * @return 1 if symmetric, else 0
 */
int symmetric(Relation *relation) {
    for (int i = 0; i < relation->size; i++) {
        int has_sym = 0;
        for (int j = 0; j < relation->size; j++) {
            if (!strcmp(relation->pairs[i].from_el, relation->pairs[j].to_el)
                && !strcmp(relation->pairs[i].to_el, relation->pairs[j].from_el)) {
                has_sym = 1;
            }
        }
        if (!has_sym) {
            return 0;
        }
    }
    return 1;
}

/**
 * Checks if the relation is antisymmetric. If for some x, y (x y) and (y x) are true, then only
 * if x == y
 * @param relation relation
 * @return 1 if antisymmetric, else 0
 */
int antisymmetric(Relation *relation) {
    for (int i = 0; i < relation->size; i++) {
        int has_sym = 0;
        for (int j = 0; j < relation->size; j++) {
            if (!strcmp(relation->pairs[i].from_el, relation->pairs[j].to_el)
                && !strcmp(relation->pairs[i].to_el, relation->pairs[j].from_el)
                && strcmp(relation->pairs[i].from_el, relation->pairs[i].to_el) != 0) {
                has_sym = 1;
            }
        }
        if (has_sym) {
            return 0;
        }
    }
    return 1;
}

/**
 * Checks if the relation is transitive, i.e. that for each x, y, z the existence of (x y) (y z) (x z)
 * @param relation
 * @return
 */
int transitive(Relation *relation) {
    for (int i = 0; i < relation->size; i++) {
        for (int j = 0; j < relation->size; j++) {
            int has_trans = 0;
            for (int k = 0; k < relation->size; k++) {
                if (!strcmp(relation->pairs[i].from_el, relation->pairs[k].from_el)
                    && !strcmp(relation->pairs[j].to_el, relation->pairs[k].to_el))
                    has_trans = 1;
            }
            if (!has_trans) {
                return 0;
            }
        }
    }
    return 1;
}

/**
 * Checks if the relation is a function. Relation is a function if a domain element is translated into only one codomain element
 * @param relation relation
 * @return 1 if function, else 0
 */
int function(Relation *relation) {
    Set *d = domain(relation);
    int res = relation->size > d->size;
    free_set(d);
    return !res;
}

/**
 * Checks if the function is injective with respect to the sets a and b.
 * The function is injective if for y from B there is no more than one argument x from A.
 * @param relation relation
 * @param a first set
 * @param b second set
 * @return 1 if injective, else 0
 */
int injective(Relation *relation, Set *a, Set *b) {
    if (!function(relation)) {
        return 0;
    }
    for (int i = 0; i < b->size; i++) {
        int income_count = 0;
        for (int j = 0; j < a->size; j++) {
            for (int k = 0; k < relation->size; k++) {
                if (!strcmp(b->items[i], relation->pairs[k].to_el) &&
                    !strcmp(a->items[j], relation->pairs[k].from_el)) {
                    income_count++;
                }
            }
        }
        if (income_count > 1) {
            return 0;
        }
    }
    return 1;
}

/**
 * Checks if the relation is a surjection. True if for each element B there is a y in relation to which
 * at least one connection is in progress.
 * @param relation relaion
 * @param a first set
 * @param b second set
 * @return 1 if surjection, else 0
 */
int surjective(Relation *relation, Set *a, Set *b) {
    if (!function(relation)) {
        return 0;
    }
    for (int i = 0; i < b->size; i++) {
        int income_count = 0;
        for (int j = 0; j < a->size; j++) {
            for (int k = 0; k < relation->size; k++) {
                if (!strcmp(b->items[i], relation->pairs[k].to_el) &&
                    !strcmp(a->items[j], relation->pairs[k].from_el)) {
                    income_count++;
                }
            }
        }
        if (income_count > 0) {
            return 1;
        }
    }
    return 0;
}

/**
 * Checks if the relaion is a bijection. True if bijection is both injection and surjection.
 * @param relation relation
 * @param a first set
 * @param b second set
 * @return 1 if bijection, else 0
 */
int bijective(Relation *relation, Set *a, Set *b) {
    if (!function(relation)) {
        return 0;
    }
    return injective(relation, a, b) && surjective(relation, a, b);
}

//////// command functions

typedef struct {
    char* command_name;
    char** args;
    Set** sets;
    Relation **relations;
    int argSize;
} Command;

/**
 * Finds a set object in a command object by the value of the argument
 * @param command command where to search for a set
 * @param arg_index the index of the argument from the list, which denotes the ordinal number of the set
 * @return set
 */
Set *find_set(Command command, int arg_index) {
    Set *res = command.sets[to_num(command.args[arg_index]) - 1];
    if (res == NULL) {
        fprintf(stderr, "Wrong index was specified");
        exit(EXIT_FAILURE);
    }
    return res;
}

/**
 * Finds the relation object in the command object by the value of the argument
 * @param command command where to look for the relation
 * @param arg_index the index of the argument from the list, which denotes the index of the relation
 * @return relation
 */
Relation *find_relation(Command command, int arg_index) {
    Relation *res = command.relations[to_num(command.args[arg_index]) - 1];
    if (res == NULL) {
        fprintf(stderr, "Wrong index was specified");
        exit(EXIT_FAILURE);
    }
    return res;
}

/**
 * Creates a command object from an input string
 * @param input input string
 * @param sets an array of all sets (indexes are ordinal numbers in the source file)
 * @param relations an array of all relation (indexes are ordinal numbers in the source file)
 * @return command object
 */
Command parse_command(char* input, Set **sets, Relation **relations) {
    int count = space_count(input);
    char** words = split_to_words(input, count);

    if (count == 0) {
        fprintf(stderr, "Command is not set");
        exit(EXIT_FAILURE);
    }

    if (count == 1) {
        fprintf(stderr, "Command must accept argumets");
        exit(EXIT_FAILURE);
    }

    Command command;
    command.command_name = strdup(words[0]);
    command.sets = sets;
    command.relations = relations;
    command.argSize = count - 1;
    command.args = malloc((count - 1) * sizeof(char*));
    for (int i = 0; i < count - 1; i++) {
        command.args[i] = strdup(words[i + 1]);
    }

    for (int i = 0; i < count; i++) {
        free(words[i]);
    }

    free(words);

    return command;
}

/**
 * Frees command memory
 * @param command command
 */
void free_command(Command *command) {
    for (int i = 0; i < command->argSize; i++) {
        free(command->args[i]);
    }
    free(command->args);
    free(command->command_name);
}

/**
 * Checks if there are enough arguments passed to the command
 * @param c
 * @param count
 */
void assert_args_count(Command c, int count) {
    if (c.argSize < count) {
        fprintf(stderr, "There must be more arguments");
        exit(EXIT_FAILURE);
    }
}

/**
 * Perform command action
 * @param c command to perform
 */
void performCommand(Command c) {
    if (!strcmp(c.command_name, "empty")) {
        assert_args_count(c, 1);
        printBool(empty(find_set(c, 0)));
    } else if (!strcmp(c.command_name, "card")) {
        assert_args_count(c, 1);
        printf("%d\n", card(find_set(c, 0)));
    } else if (!strcmp(c.command_name, "complement")) {
        assert_args_count(c, 1);
        Set *set = complement(find_set(c, 0), c.sets[0]);
        print_set(set);
        free_set(set);
    } else if (!strcmp(c.command_name, "union")) {
        assert_args_count(c, 2);
        Set *set = set_union(find_set(c, 0), find_set(c, 1));
        print_set(set);
        free_set(set);
    } else if (!strcmp(c.command_name, "intersect")) {
        assert_args_count(c, 2);
        Set *set = intersect(find_set(c, 0), find_set(c, 1));
        print_set(set);
        free_set(set);
    } else if (!strcmp(c.command_name, "minus")) {
        assert_args_count(c, 2);
        Set *set = minus(find_set(c, 0), find_set(c, 1));
        print_set(set);
        free_set(set);
    } else if (!strcmp(c.command_name, "subseteq")) {
        assert_args_count(c, 2);
        printBool(subseteq(find_set(c, 0), find_set(c, 1)));
    } else if (!strcmp(c.command_name, "subset")) {
        assert_args_count(c, 2);
        printBool(subset(find_set(c, 0), find_set(c, 1)));
    } else if (!strcmp(c.command_name, "equals")) {
        assert_args_count(c, 2);
        printBool(equals(find_set(c, 0), find_set(c, 1)));
    } else if (!strcmp(c.command_name, "reflexive")) {
        assert_args_count(c, 1);
        printBool(reflexive(find_relation(c, 0)));
    } else if (!strcmp(c.command_name, "symmetric")) {
        assert_args_count(c, 1);
        printBool(symmetric(find_relation(c, 0)));
    } else if (!strcmp(c.command_name, "antisymmetric")) {
        assert_args_count(c, 1);
        printBool(antisymmetric(find_relation(c, 0)));
    } else if (!strcmp(c.command_name, "transitive")) {
        assert_args_count(c, 1);
        printBool(transitive(find_relation(c, 0)));
    } else if (!strcmp(c.command_name, "function")) {
        assert_args_count(c, 1);
        printBool(function(find_relation(c, 0)));
    } else if (!strcmp(c.command_name, "domain")) {
        assert_args_count(c, 1);
        Set *d = domain(find_relation(c, 0));
        print_set(d);
        free_set(d);
    } else if (!strcmp(c.command_name, "codomain")) {
        assert_args_count(c, 1);
        Set *d = codomain(find_relation(c, 0));
        print_set(d);
        free_set(d);
    } else if (!strcmp(c.command_name, "injective")) {
        assert_args_count(c, 3);
        printBool(injective(find_relation(c, 0), find_set(c, 1), find_set(c, 2)));
    } else if (!strcmp(c.command_name, "surjective")) {
        assert_args_count(c, 3);
        printBool(surjective(find_relation(c, 0), find_set(c, 1), find_set(c, 2)));
    } else if (!strcmp(c.command_name, "bijective")) {
        assert_args_count(c, 3);
        printBool(bijective(find_relation(c, 0), find_set(c, 1), find_set(c, 2)));
    }
    else {
        fprintf(stderr, "Unknown command: %s", c.command_name);
        exit(EXIT_FAILURE);
    }

}

/////////////////////

/**
 * Checks that the file descriptor pointer is not empty
 * @param fp file descriptor
 */
void assert_fp_not_null(FILE *fp) {
    if (fp == NULL) {
        fprintf(stderr, "File can't be opened");
        exit(EXIT_FAILURE);
    }
}

/**
 * Counts the number of lines in the file
 * @param path absolute file path
 * @return the number of lines in the file
 */
int read_lines_count(char* path) {
    FILE *fp = fopen(path, "r");
    assert_fp_not_null(fp);
    int count = 0;
    int prev_c, c;
    while ((c = getc(fp)) != EOF) {
        if (c == '\n') {
            count++;
        }
        prev_c = c;
    }
    if (prev_c != '\n') {
        count++;
    }
    fclose(fp);
    return count;
}

/**
 * Reads lines from a file
 * @param path absolute file path
 * @param lines_count number of lines to read
 * @return array of strings from file
 */
char **read_lines(char* path, int lines_count) {
    FILE *fp = fopen(path, "r");
    assert_fp_not_null(fp);

    char **lines = malloc(lines_count * sizeof(char*));
    int c;
    for (int i = 0; i < lines_count; i++) {
        int l_length = 0, buf_size = 256;
        lines[i] = calloc(buf_size, sizeof(char));
        while ((c = getc(fp)) != '\n' && c != '\r' && c != EOF) {
            if (l_length > buf_size) {
                buf_size*=2;
                char* new_line = realloc(lines[i], buf_size * sizeof(char));
                lines[i] = new_line;
            }
            lines[i][l_length++] = (char) c;
        }
        if (l_length == 0) {
            i--;
            continue;
        }
        char *final_line = realloc(lines[i], l_length * sizeof(char));
        lines[i] = final_line;
    }

    fclose(fp);
    return lines;
}

/**
 * The main function of the program
 * @param argc
 * @param argv
 * @return
 */
int main(int argc, char **argv) {

    //read the number of lines in the file
    int count = read_lines_count(argv[1]);
    if (count < 1) {
        fprintf(stderr, "No content");
        exit(EXIT_FAILURE);
    }
    //read all lines
    char **lines = read_lines(argv[1], count);

    //create an array of pointers to sets and relations with max number - number of lines in the file
    Set **all_sets = malloc(count * sizeof (Set*));
    Relation **all_relations = malloc(count * sizeof (Relation*));
    //counter of object types
    char obj_ids[count];

    //general index in arrays
    int index = 0;

    //read universe
    if (lines[0][0] == 'U') {
        obj_ids[index] = 's';
        all_sets[index] = parse_universe(lines[0]);
        print_set(all_sets[index]);
        index++;
    } else {
        fprintf(stderr, "Universe should be defined first");
        exit(EXIT_FAILURE);
    }

    //read the rest og the file (starting from the second line)
    for (int i = 1; i < count; i++) {
        if (lines[i][0] == 'S') { // if the string is a set
            Set *set = parse_set(lines[i], all_sets[0]);
            print_set(set);
            obj_ids[index] = 's';
            all_sets[index++] = set;
        } else if (lines[i][0] == 'R') { // if the string is a relation
            Relation *relation = parse_relation(lines[i], all_sets[0]);
            print_relation(relation);
            obj_ids[index] = 'r';
            all_relations[index++] = relation;
        } else if (lines[i][0] == 'C') { // if the string is a command
            Command command = parse_command(lines[i], all_sets, all_relations);
            performCommand(command);
            free_command(&command);
        }
    }

    //free the memory of array of sets
    for (int i = 0; i < index; i++) {
        if (obj_ids[i] == 's') {
            free_set(all_sets[i]);
        }
    }
    free(all_sets);

    //free the memory of array of relations
    for (int i = 0; i < index; i++) {
        if (obj_ids[i] == 'r') {
            free_relation(all_relations[i]);
        }
    }
    free(all_relations);

    //free the memory of input strings
    for (int i = 0; i < count; i++) {
        free(lines[i]);
    }
    free(lines);


    return 0;
}
