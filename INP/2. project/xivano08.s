; Autor reseni: Elena Ivanova xivano08

; Projekt 2 - INP 2022
; Vernamova sifra na architekture MIPS64

; DATA SEGMENT
                .data
login:          .asciiz "xivano08"  ; sem doplnte vas login
cipher:         .space  17  ; misto pro zapis sifrovaneho loginu


params_sys5:    .space  8   ; misto pro ulozeni adresy pocatku
                            ; retezce pro vypis pomoci syscall 5
                            ; (viz nize "funkce" print_string)

; CODE SEGMENT
                .text

                ; ZDE NAHRADTE KOD VASIM RESENIM


main:		     
__startinc:     
                daddi r4, r0, 0
                lb    r4, login(r22) 
                
                slti  r8, r4, 61            ;r4 < 61 -> 1, r4 > 61 -> 0
                bne   r8, r0, __quit        ;r8 != 0 (r8 == 1) -> r4 is not a-z -> quit 
                
                daddi r4, r4, 9 

                slti  r8, r4, 0x7a			;r4 < 122 -> 1, r4 > 122 -> 0
                beq   r8, r0, __sub         ;r4 > 122 -> __sub

                b __continueinc         
                
__sub:          daddi r4, r4, -26
                b __continueinc

__continueinc:  sb    r4, cipher(r22)  
			    daddi r22, r22, 1
                b __startdec 


__startdec:     
                lb    r4, login(r22) 
                
                slti  r8, r4, 61            ;r4 < 61 -> 1, r4 > 61 -> 0
                bne   r8, r0, __quit        ;r8 != 0 (r8 == 1) -> r4 is not a-z -> quit 
                
                daddi r4, r4, -22 

                slti  r8, r4, 0x61			;r4 < 61 -> 1, r4 > 61 -> 0
                bne   r8, r0, __add         ;r4 < 61 -> __add

                b __continuedec         
                
__add:          daddi r4, r4, 26
                b __continuedec

__continuedec:  sb    r4, cipher(r22)  
			     daddi r22, r22, 1
                b __startinc 
                
   
__quit:         daddi r4, r0, cipher
                jal   print_string
                syscall 0                

print_string:   ; adresa retezce se ocekava v r4
                sw      r4, params_sys5(r0)
                daddi   r14, r0, params_sys5    ; adr pro syscall 5 musi do r14
                syscall 5   ; systemova procedura - vypis retezce na terminal
                jr      r31 ; return - r31 je urcen na return address
