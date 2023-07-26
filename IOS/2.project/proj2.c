#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <sys/mman.h>
#include <semaphore.h>
#include <sys/wait.h>
#include <time.h>

char *outputfilename = "proj2.out";
FILE **outputfile;

sem_t *mutex, *call, *queue_dec, *letters, *packages, *money, *worker, *queue_s;
int NZ, NU, TZ, TU, F;


void semaphore_init(){
    mutex = mmap(NULL, sizeof(sem_t), PROT_READ | PROT_WRITE, MAP_SHARED | MAP_ANONYMOUS, -1, 0);
    call = mmap(NULL, sizeof(sem_t), PROT_READ | PROT_WRITE, MAP_SHARED | MAP_ANONYMOUS, -1, 0);
    // for customer waiting for call
    letters = mmap(NULL, sizeof(sem_t), PROT_READ | PROT_WRITE, MAP_SHARED | MAP_ANONYMOUS, -1, 0);
    // for worker who chose "letters" queue to call customer from "letters" queue
    packages = mmap(NULL, sizeof(sem_t), PROT_READ | PROT_WRITE, MAP_SHARED | MAP_ANONYMOUS, -1, 0);
    // same as for letters, but for packages queue
    money = mmap(NULL, sizeof(sem_t), PROT_READ | PROT_WRITE, MAP_SHARED | MAP_ANONYMOUS, -1, 0);
    // same as for letter but for money queue
    worker = mmap(NULL, sizeof(sem_t), PROT_READ | PROT_WRITE, MAP_SHARED | MAP_ANONYMOUS, -1, 0);
    // for worker to start serving a customer, after the customer was called
    queue_s = mmap(NULL, sizeof(sem_t), PROT_READ | PROT_WRITE, MAP_SHARED | MAP_ANONYMOUS, -1, 0);
    // for correct general queue incrementing
    queue_dec = mmap(NULL, sizeof(sem_t), PROT_READ | PROT_WRITE, MAP_SHARED | MAP_ANONYMOUS, -1, 0);
    // for queue to decrement after one worker chose his queue and before another worker will choose queue


    sem_init(mutex,1,1);
    sem_init(call, 1, 0);
    sem_init(letters, 1, 0);
    sem_init(packages, 1, 0);
    sem_init(money, 1, 0);
    sem_init(worker, 1, 0);
    sem_init(queue_s, 1, 1);
    sem_init(queue_dec, 1, 1);

}

void free_sem(){

    sem_destroy(letters);
    sem_destroy(packages);
    sem_destroy(money);
    sem_destroy(mutex);
    sem_destroy(call);
    sem_destroy(worker);
    sem_destroy(queue_s);
    sem_destroy(queue_dec);

    munmap(mutex,sizeof(sem_t));
    munmap(call, sizeof(sem_t));
    munmap(letters, sizeof(sem_t));
    munmap(packages, sizeof(sem_t));
    munmap(money, sizeof(sem_t));
    munmap(worker,sizeof(sem_t));
    munmap(queue_s, sizeof(sem_t));
    munmap(queue_dec, sizeof(sem_t));

}
// worker leaves
void U_going_home(int *line, int i) {
    sem_wait(mutex);
    fprintf(*outputfile,"%d: U %d: going home\n", (*line)++, i + 1);
    fflush(*outputfile);
    sem_post(mutex);
}
// worker takes a break
void U_break(int *line, int i) {
    sem_wait(mutex);
    fprintf(*outputfile,"%d: U %d: taking a break\n", (*line)++, i + 1);
    fflush(*outputfile);
    sem_post(mutex);

    srand(getpid());
    usleep((rand() % (TU + 1)) * 1000);

    sem_wait(mutex);
    fprintf(*outputfile,"%d: U %d: break finished\n", (*line)++, i + 1);
    fflush(*outputfile);
    sem_post(mutex);
}



int main(int argc, char **argv) {

    outputfile = mmap(NULL, sizeof(FILE*), PROT_READ | PROT_WRITE, MAP_SHARED | MAP_ANONYMOUS, -1, 0);
    *outputfile = fopen(outputfilename, "w");

    //reading arguments and checking if they are correct
    if (argc < 6) {
        fprintf(stderr, "Error: Not enough arguments.\n");
        return 1;
    }

    NZ = (int) strtol(argv[1], NULL, 10);
    NU = (int) strtol(argv[2], NULL, 10);
    TU = (int) strtol(argv[3], NULL, 10);
    TZ = (int) strtol(argv[4], NULL, 10);
    F = (int) strtol(argv[4], NULL, 10);

    if (NU <= 0) {
        fprintf(stderr, "Error: Invalid numbers of workers.\n");
        return 1;
    }

    if (NZ < 0) {
        fprintf(stderr, "Error: Invalid numbers of customers.\n");
        return 1;
    }

    if (TU < 0 || TZ < 0 || F < 0) {
        fprintf(stderr, "Error: Invalid time.\n");
        return 1;
    }

    semaphore_init();

    int *line = mmap(NULL, sizeof(int), PROT_READ | PROT_WRITE, MAP_SHARED | MAP_ANONYMOUS, -1, 0);;
    *line = 1; // number of line
    int *letters_q = mmap(NULL, sizeof(int), PROT_READ | PROT_WRITE, MAP_SHARED | MAP_ANONYMOUS, -1, 0);;
    *letters_q = 0; // number of customers in letters queue
    int *packages_q = mmap(NULL, sizeof(int), PROT_READ | PROT_WRITE, MAP_SHARED | MAP_ANONYMOUS, -1, 0);;
    *packages_q = 0; //number of customers in packages queue
    int *money_q = mmap(NULL, sizeof(int), PROT_READ | PROT_WRITE, MAP_SHARED | MAP_ANONYMOUS, -1, 0);;
    *money_q = 0; //number of customers in money queue
    int *opened = mmap(NULL, sizeof(int), PROT_READ | PROT_WRITE, MAP_SHARED | MAP_ANONYMOUS, -1, 0);;
    *opened = 0; // check if the post office is opened to start oing actions
    int *queue = mmap(NULL, sizeof(int), PROT_READ | PROT_WRITE, MAP_SHARED | MAP_ANONYMOUS, -1, 0);;
    *queue = 0; // general queue
    int *closed = mmap(NULL, sizeof(int), PROT_READ | PROT_WRITE, MAP_SHARED | MAP_ANONYMOUS, -1, 0);;
    *closed = 0; // check if the post office is closed
    int *processes = mmap(NULL, sizeof(int), PROT_READ | PROT_WRITE, MAP_SHARED | MAP_ANONYMOUS, -1, 0);;
    *processes = 0; // to wait for all processes to end


    pid_t pid;

    // create worker processes
    for (int i = 0; i < NU; i++) {
        pid = fork();
        if (pid == -1) {
            perror("Error: fork");
            exit(1);
        } else if (pid == 0) {

            sem_wait(mutex);
            fprintf(*outputfile,"%d: U %d: started\n", (*line)++, i + 1);
            fflush(*outputfile);
            sem_post(mutex);

            while (1) {
                if (*opened) {
                    if (*queue > 0) {

                        int service_type = 0;

                        sem_wait(queue_dec);
                        if (*queue == 0) { //check the queue
                            sem_post(queue_dec);
                            if (!*closed) {
                                U_break(line, i);
                                continue;
                            } else {
                                U_going_home(line, i);
                                (*processes)++;
                                exit(0);
                            }
                        }


                        while (service_type == 0) { // choose the service type (letter, packages, money)
                            srandom(time(NULL));
                            int random_num = random() %1000000;
                            srandom(random_num);
                            service_type = random() % 3 + 1;

                            if (service_type == 1 && (*letters_q) == 0) {
                                service_type = 0;
                            } else if (service_type == 2 && (*packages_q) == 0) {
                                service_type = 0;
                            } else if (service_type == 3 && (*money_q) == 0) {
                                service_type = 0;

                            }

                        }

                        switch (service_type) { // call for customer from chosen queue
                            case 1:
                                sem_post(letters);
                                break;
                            case 2:
                                sem_post(packages);
                                break;
                            case 3:
                                sem_post(money);
                                break;
                            default: return 1;
                        }

                        sem_post(call);

                        sem_wait(worker);
                        sem_wait(mutex);
                        fprintf(*outputfile,"%d: U %d: serving a service of type %d\n", (*line)++, i + 1, service_type);
                        fflush(*outputfile);
                        sem_post(mutex);

                        srand(getpid());
                        usleep((rand() % (10 + 1)) * 1000);

                        sem_wait(mutex);
                        fprintf(*outputfile,"%d: U %d: service finished\n", (*line)++, i + 1);
                        fflush(*outputfile);
                        sem_post(mutex);

                    } else {
                        if (*closed != 1 && *queue == 0) {
                            U_break(line, i);
                            continue;
                        } else if (*closed == 1 && *queue == 0) {
                            U_going_home(line, i);
                            (*processes)++;
                            exit(0);
                        }
                    }
                }
            }
        }
    }

    // create zakaznik processes
    for (int i = 0; i < NZ; i++) {
        pid = fork();
        if (pid == -1) {
            perror("fork");
            exit(1);
        } else if (pid == 0) {

            sem_wait(mutex);
            fprintf(*outputfile,"%d: Z %d: started\n", (*line)++, i + 1);
            fflush(*outputfile);
            sem_post(mutex);

            srand(time(NULL));
            usleep((rand() % (TZ + 1)) * 1000);

            if (*closed) { // check if the post office is closed
                sem_wait(mutex);
                fprintf(*outputfile,"%d: Z %d: going home\n", (*line)++, i + 1);
                fflush(*outputfile);
                sem_post(mutex);
                (*processes)++;
                exit(0);
            }

            srand(getpid());
            int service_type = rand() % 3 + 1;

            sem_wait(queue_s);
            sem_wait(mutex);
            (*queue)++;
            fprintf(*outputfile,"%d: Z %d: entering office for a service %d\n", (*line)++, i + 1, service_type);
            fflush(*outputfile);
            sem_post(queue_s);
            sem_post(mutex);

            switch (service_type) {
                case 1:
                    (*letters_q)++;
                    sem_wait(letters);
                    break;
                case 2:
                    (*packages_q)++;
                    sem_wait(packages);
                    break;
                case 3:
                    (*money_q)++;
                    sem_wait(money);
                    break;
                default: return 1;
            }

            sem_wait(call);

            sem_wait(mutex);
            switch (service_type) {
                case 1:
                    (*letters_q)--;
                    (*queue)--;
                    break;
                case 2:
                    (*packages_q)--;
                    (*queue)--;
                    break;
                case 3:
                    (*money_q)--;
                    (*queue)--;
                    break;
                default: return 1;
            }
            sem_post(queue_dec);
            fprintf(*outputfile,"%d: Z %d: called by office worker\n", (*line)++, i + 1);
            fflush(*outputfile);
            sem_post(worker);
            sem_post(mutex);

            srand(time(NULL));
            usleep((rand() % (10 + 1)) * 1000);

            sem_wait(mutex);
            fprintf(*outputfile,"%d: Z %d: going home\n", (*line)++, i + 1);
            fflush(*outputfile);
            sem_post(mutex);
            (*processes)++;
            exit(0);

        }
    }

    *opened = 1;

    srand(time(NULL));
    usleep(((rand() % (F / 2 + 1)) + F / 2) * 1000);

    *closed = 1;
    fprintf(*outputfile,"%d: closing\n", (*line)++);
    fflush(*outputfile);
    while (1) {
        if (*processes == NU + NZ) { // wait for processes to exit
            free_sem();

            for (int i = 0; i < NU + NZ; i++) {
                int status;
                waitpid(-1, &status, 0);
            }

            munmap(line, sizeof(int));
            munmap(queue, sizeof(int));
            munmap(letters_q, sizeof(int));
            munmap(packages_q, sizeof(int));
            munmap(money_q, sizeof(int));
            munmap(opened, sizeof(int));
            munmap(closed, sizeof(int));

            exit(0);
        }
    }
}

