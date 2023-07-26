--cpu.vhd: Simple 8-bit CPU (BrainFuck interpreter)
-- Copyright (C) 2022 Brno University of Technology,
--                    Faculty of Information Technology
-- Author(s): Elena Ivanova <xivano08>
--
library ieee;
use ieee.std_logic_1164.all;
use ieee.std_logic_arith.all;
use ieee.std_logic_unsigned.all;

-- ----------------------------------------------------------------------------
--                        Entity declaration
-- ----------------------------------------------------------------------------
entity cpu is
 port (
   CLK   : in std_logic;  -- hodinovy signal
   RESET : in std_logic;  -- asynchronni reset procesoru
   EN    : in std_logic;  -- povoleni cinnosti procesoru
 
   -- synchronni pamet RAM
   DATA_ADDR  : out std_logic_vector(12 downto 0); -- adresa do pameti
   DATA_WDATA : out std_logic_vector(7 downto 0); -- mem[DATA_ADDR] <- DATA_WDATA pokud DATA_EN='1'
   DATA_RDATA : in std_logic_vector(7 downto 0);  -- DATA_RDATA <- ram[DATA_ADDR] pokud DATA_EN='1'
   DATA_RDWR  : out std_logic;                    -- cteni (0) / zapis (1)
   DATA_EN    : out std_logic;                    -- povoleni cinnosti
   
   -- vstupni port
   IN_DATA   : in std_logic_vector(7 downto 0);   -- IN_DATA <- stav klavesnice pokud IN_VLD='1' a IN_REQ='1'
   IN_VLD    : in std_logic;                      -- data platna
   IN_REQ    : out std_logic;                     -- pozadavek na vstup data
   
   -- vystupni port
   OUT_DATA : out  std_logic_vector(7 downto 0);  -- zapisovana data
   OUT_BUSY : in std_logic;                       -- LCD je zaneprazdnen (1), nelze zapisovat
   OUT_WE   : out std_logic                       -- LCD <- OUT_DATA pokud OUT_WE='1' a OUT_BUSY='0'
 );
end cpu;


-- ----------------------------------------------------------------------------
--                      Architecture declaration
-- ----------------------------------------------------------------------------
architecture behavioral of cpu is

    type FSM_state is(
        start,
        fetch,
        decode,

        ptr_inc_f, ptr_inc_f2, ptr_inc_f3,
        ptr_dec_f, ptr_dec_f2, ptr_dec_f3,

        inc_f1, inc_f2, inc_f3,           
        dec_f1, dec_f2, dec_f3,
        
        while_f1, while_f2, while_f3, while_f4, while_f5,
        while_end_f1, while_end_f2, while_end_f3, while_end_f4, while_end_f5, while_end_f6, while_end_f7, while_end_f8,

        do_while_end_f1, do_while_end_f2, do_while_end_f3, do_while_end_f4, do_while_end_f5, do_while_end_f6, do_while_end_f7, do_while_end_f8,
        
        putchar_f1, putchar_f2, putchar_f3,

        getchar_f1, getchar_f2, getchar_f3,

        return_f,
        other, other2,other3

    );
    --program
    signal PC_inc: std_logic;
    signal PC_dec: std_logic;
    signal PC_reg: std_logic_vector(12 downto 0):= "0000000000000";
    signal PC_clear: std_logic;
    --counter
    signal CNT_inc: std_logic;
    signal CNT_dec: std_logic;
    signal CNT_reg: std_logic_vector(12 downto 0):="0000000000000";
    signal CNT_clear: std_logic;
    --data
    signal PTR_inc: std_logic;
    signal PTR_dec: std_logic;
    signal PTR_reg: std_logic_vector(12 downto 0) :="0000000000000";
    signal PTR_clear: std_logic;
    --mx1
    signal MX1_sel: std_logic;
    signal MX1_out: std_logic_vector(12 downto 0):="0000000000000";
    --mx2
    signal MX2_sel: std_logic_vector(1 downto 0);
    signal MX2_out: std_logic_vector(7 downto 0):="00000000";

    signal pstate: FSM_state := start; -- current state
    signal nstate: FSM_state; -- next state

begin

    pcCounter: process (CLK, RESET, PC_inc, PC_dec)
    begin
        if RESET = '1' then
            PC_reg <= (others => '0');
        end if;
        if rising_edge(clk) then
            if PC_inc = '1' then
                PC_reg <= PC_reg + 1;
            elsif PC_dec = '1' then
                PC_reg <= PC_reg - 1;
            elsif PC_clear = '1' then
                PC_reg <= "0000000000000";
            end if;
        end if;
    end process pcCounter;

    ptrCounter: process (CLK, RESET, PTR_inc, PTR_dec)
	begin
		if RESET = '1' then
			PTR_reg <= (12 => '1', others => '0');
        end if;
		if rising_edge(clk) then
			if PTR_inc = '1' then
				PTR_reg <= PTR_reg + 1;
			elsif PTR_dec = '1' then
				PTR_reg <= PTR_reg - 1;
			elsif PTR_clear = '1' then
				PTR_reg <= "0000000000000";
			end if;
		end if;
	end process ptrCounter;

    cntCounter: process (CLK, RESET, CNT_inc, CNT_dec)
	begin
		if RESET = '1' then
			CNT_reg <= (others => '0');
        end if;
		if rising_edge(clk) then
			if CNT_inc = '1' then
				CNT_reg <= CNT_reg + 1;
			elsif CNT_dec = '1' then
				CNT_reg <= cnt_reg - 1;
			elsif CNT_clear = '1' then
				CNT_reg <= "0000000000000";
			end if;
		end if;
	end process cntCounter;

    fsm_pstate: process (CLK, RESET, EN)
	begin
		if RESET = '1' then
			pstate <= start;
		end if;
        if rising_edge(CLK) then
			if EN = '1' then
				pstate <= nstate;
			end if;
		end if;
	end process fsm_pstate;

    MX1_process: process (CLK, RESET, PC_reg, PTR_reg, MX1_sel)
    begin
        if RESET = '1' then
            MX1_out <= (others => '0');
        elsif rising_edge(CLK) then
            case MX1_sel is
                when '0' =>
                    MX1_out <= PC_reg;
                when '1' =>
                    MX1_out <= PTR_reg;
                when others =>
                    MX1_out <= (others => '0');
            end case;
        end if;
    end process MX1_process;

	MX2_process: process (CLK, RESET, MX2_sel, DATA_RDATA, IN_DATA)
	begin
		if RESET = '1' then
			MX2_out <= (others => '0');
		elsif rising_edge(CLK) then
			case MX2_sel is
				when "00" =>
					MX2_out <= IN_DATA;
				when "01" =>
					MX2_out <= DATA_RDATA - 1;
				when "10" =>
					MX2_out <= DATA_RDATA + 1;
				when others =>
					MX2_out <= X"00";
			end case;
		end if;
	end process MX2_process;

    DATA_ADDR <= mx1_out;
    DATA_WDATA <= mx2_out;
    OUT_DATA <= DATA_RDATA;

    fsm_nstate: process (pstate, OUT_BUSY, IN_VLD, CNT_reg, DATA_RDATA)
    begin 
    
    PC_inc <= '0';
    PC_dec <= '0';
    PC_clear <= '0';
    PTR_inc <= '0';
    PTR_dec <= '0';
    PTR_clear <= '0';
    CNT_inc <= '0';
    CNT_dec <= '0';
    CNT_clear <= '0';
    MX1_sel <= '0'; -- pc
    MX2_sel <= "11"; 
    DATA_RDWR <= '0';
    DATA_EN <= '0';
    OUT_WE <= '0';
    IN_REQ <= '0';


    case pstate is
        when start =>
            MX1_sel <= '0'; --pc
            DATA_RDWR <= '0'; 
            DATA_EN <= '1';
            nstate <= fetch;
            
			when fetch =>
				MX1_sel <= '1'; --ptr
				DATA_RDWR <= '0';
				DATA_EN <= '1';

				nstate <= decode;

            when decode =>
                case DATA_RDATA is
                    when X"3E" =>           
                        nstate <= ptr_inc_f; ----->
                    when X"3C" =>
                        nstate <= ptr_dec_f; ------<
                    when X"2B" =>
                        nstate <= inc_f1; -----+
                    when X"2D" =>
                        nstate <= dec_f1; ----- -
                    when X"5B" =>
                        nstate <= while_f1; ----- [
                    when X"5D" =>
                        nstate <= while_end_f1; ----- ]
                    when X"28" => 
                        nstate <= other; ----- (
                    when X"29" => 
                        nstate <= do_while_end_f1;    ---- ) 
                    when X"2E" =>
                        nstate <= putchar_f1; ----- .
                    when X"2C" =>
                        nstate <= getchar_f1;----- ,
                    when X"00" =>
                        nstate <= return_f; 
                    when others =>
                        nstate <= other;
                end case;
                DATA_EN <= '1';
				DATA_RDWR <= '0';
				MX1_sel <= '0';
                IN_REQ <= '1';
            
            ----------other (also used for "(")

            when other =>
                nstate <= other2;
                PC_inc <= '1';  --pc-reg++          
            when other2 =>
                nstate <= other3;
                MX1_sel <= '0'; --pc
            when other3 =>
                DATA_EN <= '1';
                DATA_RDWR <= '0';
                nstate <= fetch;


            -----------pointer increment-----------

            when ptr_inc_f =>
                PTR_inc <= '1';--PTR-reg++ 
                PC_inc <= '1'; --pc-reg++ 
                nstate <= ptr_inc_f2;

            when ptr_inc_f2 =>
                nstate <= ptr_inc_f3;
                MX1_sel <= '0'; --pc
            when ptr_inc_f3 =>
                DATA_EN <= '1';
                DATA_RDWR <= '0';
                nstate <= fetch;

            
            -----------pointer decrement-------------

            when ptr_dec_f =>
                PTR_dec <= '1'; -- PTR-reg--
                PC_inc <= '1'; --pc-reg++ 
                nstate <= ptr_dec_f2;

            when ptr_dec_f2 =>
                MX1_sel <= '0'; --pc
                nstate <= ptr_dec_f3;
            
            when ptr_dec_f3 =>
                DATA_EN <= '1';
                DATA_RDWR <= '0';
                nstate <= fetch;

            ----------increment----------

            when inc_f1 =>
                MX1_sel <= '1'; --ptr
                MX2_sel <= "10"; --DATA_RDATA++
                PC_inc <= '1'; --pc-reg++ 
                nstate <= inc_f2;
        
            when inc_f2 =>
                DATA_EN <= '1';
                DATA_RDWR <= '1';
                MX1_sel <= '0'; --pc
                nstate <= inc_f3;
            when inc_f3 =>
                DATA_EN <= '1';
                DATA_RDWR <= '0';
                nstate <= fetch;
            

            ----------decrement----------
            
            when dec_f1 =>
                MX1_sel <= '1'; --ptr
                MX2_sel <= "01"; --DATA_RDATA--
                PC_inc <= '1';--pc-reg++ 
                nstate <= dec_f2;
            
            when dec_f2 =>
                DATA_EN <= '1';
                DATA_RDWR <= '1';
                MX1_sel <= '0'; --pc
                nstate <= dec_f3;
            when dec_f3 =>
                DATA_EN <= '1';
                DATA_RDWR <= '0';
                nstate <= fetch;

            ----------putchar----------

            when putchar_f1 =>
			    if OUT_BUSY = '0' then 
			  	    OUT_WE <= '1'; 
			      	PC_inc <= '1'; --pc-reg++ 

			      	nstate <= putchar_f2;
			    elsif OUT_BUSY = '1' then 
			      	nstate <= putchar_f1; 
			    end if;
    
            when putchar_f2 =>
                MX1_sel <= '0'; --pc
                nstate <= putchar_f3;
            when putchar_f3 =>
                DATA_EN <= '1';
                DATA_RDWR <= '0';
                nstate <= fetch;

            ----------getchar------------ 

            when getchar_f1 =>
                MX1_sel <= '1'; --ptr
                if IN_VLD /= '1' then
                    IN_REQ <= '1';
                    nstate <= getchar_f1;
                else
                    MX2_sel <= "00"; --IN_DATA - from input
                    DATA_EN <= '1';
                    DATA_RDWR <= '0';
                    nstate <= getchar_f2;
                end if;
        
            when getchar_f2 =>
                DATA_EN <= '1';
                DATA_RDWR <= '1';
                MX1_sel <= '1';	--ptr

                PC_inc <= '1'; --pc-reg++ 
                
                nstate <= getchar_f3;
            
            when getchar_f3 =>
                nstate <= fetch;
            
            ----------while-----------

			when while_f1 =>
                PC_inc <= '1'; --pc-reg++ 
                nstate <= while_f2;
			when while_f2 =>

				if DATA_RDATA = "00000000" then 
                    MX1_sel <= '0'; --pc
                    cnt_inc <= '1';
                    nstate <= while_f3;
				else -- if DATA_RDATA != 0
                    MX1_sel <= '0';
                    nstate <= while_f5;
				end if;
			when while_f3 =>
                PC_inc <= '1'; --pc-reg++ 
                DATA_EN <= '1';
                DATA_RDWR <= '0';
            when while_f4 =>
				if CNT_reg = "0000000000000" then 
                    MX1_sel <= '0'; --pc
					nstate <= while_f5;
				else -- if CNT-reg != 0
					if DATA_RDATA = X"5B" then -- [
						CNT_inc <= '1'; --CNT-reg++
					elsif DATA_RDATA = X"5D" then --]
						CNT_dec <= '1'; --CNT-reg--
					end if;
                    MX1_sel <= '0'; --pc
					nstate <= while_f3;
				end if;
            when while_f5 =>
                DATA_EN <= '1';
                DATA_RDWR <= '0';
                nstate <= fetch;

            ------------end of while loop-----------

            when while_end_f1 =>
                PC_dec <= '1'; --pc-reg-- 
                nstate <= while_end_f2;

            when while_end_f2 =>
            if DATA_RDATA = "00000000" then 
                PC_inc <= '1'; --pc-reg++ 
                nstate <= while_end_f5;
            else 
                cnt_inc <= '1'; --cnt-reg++ 
                MX1_sel <= '0'; --pc
                nstate <= while_end_f3;
            end if;
            when while_end_f3 =>
                nstate <= while_end_f4;
                DATA_RDWR <= '0';
                PC_dec <= '1'; --pc-reg--
                DATA_EN <= '1';
            when while_end_f4 =>
                if CNT_reg = "0000000000000" then 
                    nstate <= while_end_f5;
                    PC_inc <= '1'; --pc-reg++ 
                else
                    if DATA_RDATA = X"5D" then  --- ]
                        CNT_inc <= '1'; 
                    elsif DATA_RDATA = X"5B" then --- [
                        CNT_dec <= '1'; 
                    end if;         
                    MX1_sel <= '0'; --pc
                    nstate <= while_end_f3;
                end if;
            when while_end_f5 =>
                nstate <= while_end_f6;
                PC_inc <= '1'; --pc-reg++ 
            when while_end_f6 =>
                nstate <= while_end_f7;
                PC_inc <= '1';--pc-reg++ 
            when while_end_f7 =>
                nstate <= while_end_f8;
                MX1_sel <= '0'; --pv
            when while_end_f8 =>
                DATA_RDWR <= '0';
                DATA_EN <= '1';
                nstate <= fetch;

            ---------- end of do while loop ----------

            when do_while_end_f1 =>
                PC_dec <= '1'; --pc-reg--
                nstate <= do_while_end_f2;

            when do_while_end_f2 =>
            if DATA_RDATA = "00000000" then 
                PC_inc <= '1'; --pc-reg++ 
                nstate <= do_while_end_f5;
            else 
                CNT_inc <= '1'; --cnt-reg++ 
                MX1_sel <= '0'; --pc
                nstate <= do_while_end_f3;
            end if;
            when do_while_end_f3 =>
                nstate <= do_while_end_f4;
                DATA_RDWR <= '0';
                PC_dec <= '1'; --pc-reg--
                DATA_EN <= '1';
            when do_while_end_f4 =>
                if CNT_reg = "0000000000000" then 
                    nstate <= do_while_end_f5;
                    PC_inc <= '1';--pc-reg++ 
                else
                    if DATA_RDATA = X"29" then --)
                        CNT_inc <= '1'; 
                    elsif DATA_RDATA = X"28" then  --(
                        CNT_dec <= '1';
                    end if;         
                    MX1_sel <= '0'; --pc
                    nstate <= do_while_end_f3;
                end if;
            when do_while_end_f5 =>
                nstate <= do_while_end_f6;
                PC_inc <= '1';--pc-reg++ 
            when do_while_end_f6 =>
                nstate <= do_while_end_f7;
                PC_inc <= '1';--pc-reg++ 
            when do_while_end_f7 =>
                nstate <= do_while_end_f8;
                MX1_sel <= '0'; --pc
            when do_while_end_f8 =>
                DATA_RDWR <= '0';
                DATA_EN <= '1';
                nstate <= fetch;

            when others =>
                null;

        end case;

    end process;
    
end behavioral;



