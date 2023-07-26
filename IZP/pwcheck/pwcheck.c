#include <stdio.h>
#include <stdlib.h>

//cistime pole
void clear_array(char *arr, int size) {
    for (int i=0; i < size; i++) {
        arr[i] = '\0';
    }
}

//funkce pro zobrazeni statistik
void show_stats(int min_len, float avg_len, int unique_sym) {
    printf("Statistika:\n");
    printf("Ruznych znaku: %d\n", unique_sym);
    printf("Minimalni delka: %d\n", min_len);
    printf("Prumerna delka: %.1f\n", avg_len);
}

//funkce pro overeni, zda je symbol obsazen v urcitem intervalu v tabulce ASCII
int contains_sym_from_range(const char *password, int len, int start, int end) {
    for (int i = 0; i < len; i++) {
        if (password[i] >= start && password[i] <= end) {
            return 1;
        }
    }
    return 0;
}

//soucet cisel v poli
int sum_array_values(const char *arr, int arr_len) {
    int sum = 0;
    for (int i = 0; i < arr_len; i++) {
        sum += arr[i];
    }
    return sum;
}

//maximalni delka stejnych podretezcu
int id_line(const char *password, int len) {
    int max = 0, count = 0;

    for (int i = 0; i < len; i++) {
        for (int j = 0; j < i; j++) {
            if (password[j] == password[i]) {
                ++count;
                ++i;
            }else count = 0;
            if (count > max) {
                max = count;
            }
        }
    }
    return max;
}

//maximalni delka sekvence stejnych znaku
int id_sym(const char *password, int len) {
    int count = 1, max = 0;
    for (int i = 0; i < len; i++) {
        if (password[i] == password[i - 1]) {
            ++count;
            }else count = 1;
        if (count > max) {
            max = count;
        }
    }
    return max;
}

// overeni. zda heslo obsahuje mala pismena
int contains_upper(const char *password, int len) {
    return contains_sym_from_range(password, len, 65, 90);
}

// overeni, zda heslo obsahuje velka pismena
int contains_lower(const char *password, int len) {
    return contains_sym_from_range(password, len, 97, 122);
}

// overeni, zda heslo obsahuje cisla
int contains_numbers(const char *password, int len) {
    return contains_sym_from_range(password, len, 48, 57);
}

// overeni, zda heslo obsahuje specialni znaky
int contains_specials(const char *password, int len) {
    return contains_sym_from_range(password, len, 32, 47)
           ||contains_sym_from_range(password, len, 58, 64)
           || contains_sym_from_range(password, len, 91, 96)
           || contains_sym_from_range(password, len, 123, 126);
}


// overeni, zda heslo splnuje pravidla
int meets_requirements(const char *password, int len, int level, int param) {
    int meets = 0;

    if (level >= 1) {
        if (contains_lower(password, len) && contains_upper(password, len)) {
            meets = 1;
        } else return 0;
    }

    if (level >= 2) {
        int groups_count =
                contains_upper(password, len)
                + contains_lower(password, len)
                + contains_numbers(password, len)
                + contains_specials(password, len);
        if ((groups_count >= param) || (param > 4 && groups_count == 4)) {
            meets = 1;
        } else return 0;
    }

    if (level >= 3) {
        if (id_sym(password, len) < param) {
            meets = 1;
        }else return 0;
    }

    if (level >= 4) {
        if (id_line(password, len) < param) {
            meets = 1;
        }else return 0;
    }

    return meets;
}

//overeni, ze 3. argument je "--stats"
int check_stats (char **argv) {
    int check = 0;
    if (argv[3][0] == '-' &&
        argv[3][1] == '-' &&
        argv[3][2] == 's' &&
        argv[3][3] == 't' &&
        argv[3][4] == 'a' &&
        argv[3][5] == 't' &&
        argv[3][6] == 's' &&
        argv[3][7] == '\0') {
        check = 1;
    }return check;
}


int main(int argc, char **argv) {
    if (argc < 3 || argc > 4) {
        fprintf(stderr, "%d", '\0');
        return 1;
    }

    int use_stats = argc == 4;
    if (argc == 4 && check_stats(argv) != 1) {
        fprintf(stderr, "%d", '\0');
        return 1;
    }

    int level = (int) strtol(argv[1], NULL, 10),
        param = (int) strtol(argv[2], NULL, 10);

    if (level < 1 || level > 4) {
        fprintf(stderr, "%d", '\0');
        return 1;
    }

    if (param < 1) {
        fprintf(stderr, "%d", '\0');
        return 1;
    }

    int i = 0, c, len, count = 0, all_sym_count = 0;

    char password_candidate[10000];
    int min_length = 100;
    char symbols[255];
    clear_array(symbols, 255);

    while ((c = getchar()) != EOF) {
        if (c != '\n') {
            password_candidate[i++] = (char) c;
            symbols[c] = 1;
            all_sym_count ++;
        } else {
            len = i;
            if (len > 100) {
                fprintf(stderr, "%d", '\0');
                return 1;
            }


            if (len < min_length)
                min_length = len;

            i = 0;

            if (meets_requirements(password_candidate, len, level, param)) {
                printf("%s\n", password_candidate);
            }

            clear_array(password_candidate, 100);
            count++;
        }
    }
    if (i != 0) {
        len = i;
        if (len > 100) {
            fprintf(stderr, "%d", '\0');
            return 1;
        }

        if (len < min_length)
            min_length = len;

        if (meets_requirements(password_candidate, len, level, param)) {
            printf("%s\n", password_candidate);
        }

        clear_array(password_candidate, 100);
        count++;
    }

    //zobrazeni statistik

     if (use_stats) {
         show_stats(min_length,
                        ((float) all_sym_count) / (float) count,
                        sum_array_values(symbols, 255));
        }

    return 0;
}
