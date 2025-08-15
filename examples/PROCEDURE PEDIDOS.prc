CREATE OR REPLACE PROCEDURE sp_inserePedidoSitemercado (
p_nropedidoafv IN consinco.edi_pedvendacliente.nropedidoafv%type,
p_nroempresa IN consinco.edi_pedvendacliente.nroempresa%type,
p_nrocgccpf IN consinco.edi_pedvendacliente.nrocgccpf%type,
p_digcgccpf IN consinco.edi_pedvendacliente.digcgccpf%type,
p_nomerazao IN consinco.edi_pedvendacliente.nomerazao%type,
p_fantasia IN consinco.edi_pedvendacliente.fantasia%type,
p_fisicajuridica IN consinco.edi_pedvendacliente.fisicajuridica%type,
p_sexo IN consinco.edi_pedvendacliente.sexo%type,
p_cidade IN consinco.edi_pedvendacliente.cidade%type,
p_uf IN consinco.edi_pedvendacliente.uf%type,
p_bairro IN consinco.edi_pedvendacliente.bairro%type,
p_logradouro IN consinco.edi_pedvendacliente.logradouro%type,
p_nrologradouro IN consinco.edi_pedvendacliente.nrologradouro%type,
p_cmpltologradouro IN consinco.edi_pedvendacliente.cmpltologradouro%type,
p_cep IN consinco.edi_pedvendacliente.cep%type,
p_foneddd1 IN consinco.edi_pedvendacliente.foneddd1%type,
p_fonenro1 IN consinco.edi_pedvendacliente.fonenro1%type,
p_foneddd2 IN consinco.edi_pedvendacliente.foneddd2%type,
p_fonenro2 IN consinco.edi_pedvendacliente.fonenro2%type,
p_inscricaorg IN consinco.edi_pedvendacliente.inscricaorg%type,
p_dtanascfund IN consinco.edi_pedvendacliente.dtanascfund%type,
p_email IN consinco.edi_pedvendacliente.email%type,
p_emailnfe IN consinco.edi_pedvendacliente.emailnfe%type,
p_indentregaretira IN consinco.edi_pedvenda.indentregaretira%type,
p_dtapedidoafv IN consinco.edi_pedvenda.dtapedidoafv%type,
p_vlrtotfrete IN consinco.edi_pedvenda.vlrtotfrete%type,
p_valor IN consinco.edi_pedvendaformapagto.valor%type,
p_nroformapagto IN consinco.edi_pedvenda.nroformapagto%type,
p_usuinclusao IN consinco.edi_pedvenda.usuinclusao%type,
p_nroparcelas IN consinco.edi_pedvenda.nroparcelas%type,
p_codoperadoracartao IN consinco.edi_pedvenda.codoperadoracartao%type,
p_nrocartao IN consinco.edi_pedvenda.nroformapagto%type
)
IS
vnseqedipedvenda number;
BEGIN

-- #### ROTINA DE EXCLUS�O DA SITE MERCADO #######--

  FOR X IN( SELECT MAX(AA.SEQEDIPEDVENDA) as SEQEDIPEDVENDA
            FROM CONSINCO.EDI_PEDVENDA AA
            WHERE AA.NROPEDIDOAFV = p_nropedidoafv
            )

            LOOP

DELETE FROM consinco.edi_pedvendaformapagto
 WHERE seqedipedvenda = X.SEQEDIPEDVENDA;

DELETE FROM consinco.edi_pedvendaitem
 WHERE seqedipedvenda = X.SEQEDIPEDVENDA;

DELETE FROM consinco.edi_pedvenda
WHERE seqedipedvenda = X.SEQEDIPEDVENDA;

DELETE FROM consinco.edi_pedvendacliente
 WHERE nropedidoafv = p_nropedidoafv;

 END LOOP;


 -- ## faz a inser��o com o que site mercado envia ## --

 select consinco.s_edi_pedvenda.nextval
 into vnseqedipedvenda from dual;

INSERT INTO consinco.edi_pedvendacliente (
nropedidoafv,
nroempresa,
statusimportacao,
nrocgccpf,
digcgccpf,
nomerazao,
fantasia,
fisicajuridica,
sexo,
cidade,
uf,
pais,
bairro,
logradouro,
nrologradouro,
cmpltologradouro,
cep,
foneddd1,
fonenro1,
foneddd2,
fonenro2,
inscricaorg,
dtanascfund,
email,
emailnfe)
VALUES (
p_nropedidoafv,
p_nroempresa,
'P',
p_nrocgccpf,
p_digcgccpf,
consinco.fc5limpaacento(trim(p_nomerazao)),
consinco.fc5limpaacento(trim(p_fantasia)),
p_fisicajuridica,
p_sexo,
consinco.fc5limpaacento(trim(p_cidade)),
p_uf,
'Brasil',
consinco.fc5limpaacento(trim(p_bairro)),
consinco.fc5limpaacento(trim(p_logradouro)),
consinco.fc5limpaacento(trim(p_nrologradouro)),
consinco.fc5limpaacento(trim(p_cmpltologradouro)),
p_cep,
p_foneddd1,
p_fonenro1,
p_foneddd2,
p_fonenro2,
p_inscricaorg,
p_dtanascfund,
trim(p_email),
trim(p_emailnfe));


INSERT INTO consinco.edi_pedvenda (
seqedipedvenda,
nropedidoafv,
nropedcliente,
nroempresa,
codsistemaafv,
seqpessoa,
nroformapagto,
tippedido,
obspedido,
usuinclusao,
dtainclusao,
indentregaretira,
dtapedidoafv,
nroparcelas,
indecommerce,
vlrtotfrete,
tiporateiofreteped,
codoperadoracartao,
codgeraloper,
nrosegmento,
nrotabvenda )
VALUES (
vnseqedipedvenda/*p_nropedidoafv*/,
p_nropedidoafv,
cast(p_nropedidoafv as varchar2(20)),
p_nroempresa,
0,
0,
p_nroformapagto, -- PASSAR PARA A SITE MERCADO POIS VEM DO INTEGRADOR
'V',
'n/a',
'OMS-SUPREAL', -- PASSAR FIXO ALGUM USU�RIO OU SOLICITAR QUE A SITE MERCADO PASSE NA CHAMADA
sysdate,
p_indentregaretira,
p_dtapedidoafv,
p_nroparcelas,
'S', ----- COLOCADO 'S' em 14/04 para teste antes estava ''
p_vlrtotfrete,
'V',
p_codoperadoracartao,
894, -- CGO � FIXO - FIXO
2,   -- SEGMENT � FIXO - FIXO
1);  -- TABELA DE VENDA � FIXA

INSERT INTO consinco.edi_pedvendaformapagto (
seqedipedvenda,
nroformapagto,
nrocondpagto,
valor,
codoperadoracartao,
nrogiftcard,
nrocartao,
nroparcela)
VALUES (
vnseqedipedvenda/*p_nropedidoafv*/,
102, -- FIXO
102, -- INFORMAR A CONDI��O DE PAGAMENTO � FIXO NO PEDIDO
p_valor,
p_codoperadoracartao,
NULL,
p_nrocartao,
1);
COMMIT;
END;

