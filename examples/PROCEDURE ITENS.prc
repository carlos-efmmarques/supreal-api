CREATE OR REPLACE PROCEDURE sp_insereItensSitemercado (
       p_seqedipedvenda IN consinco.edi_pedvendaitem.seqedipedvenda%type,
       p_seqpedvendaitem IN consinco.edi_pedvendaitem.seqpedvendaitem%type,
       p_codacesso IN consinco.edi_pedvendaitem.codacesso%type,
       p_seqproduto IN consinco.edi_pedvendaitem.seqproduto%type,
       p_qtdpedida IN consinco.edi_pedvendaitem.qtdpedida%type,
       p_qtdembalagem IN consinco.edi_pedvendaitem.qtdembalagem%type,
       p_vlrembtabpreco IN consinco.edi_pedvendaitem.vlrembtabpreco%type,
       p_vlrembinformado IN consinco.edi_pedvendaitem.vlrembinformado%type
)
IS
vnseqedipedvenda number;
BEGIN
   SELECT MAX(AA.SEQEDIPEDVENDA)  
            into vnseqedipedvenda
            FROM CONSINCO.EDI_PEDVENDA AA
            WHERE AA.NROPEDIDOAFV = p_seqedipedvenda;
            -- na variavel p_seqedipedvenda vem o nropedidoafv da edi, pois a site mercado tratava nropedidoafv e seqedipedvenda iguais
      
     INSERT INTO consinco.edi_pedvendaitem (
       seqedipedvenda,
       seqpedvendaitem,
       codacesso,
       seqproduto,
       nrocondicaopagto,
       qtdpedida,
       qtdembalagem,
       vlrembtabpreco,
       vlrembinformado,
       nrotabvenda)
      VALUES (
       vnseqedipedvenda  /*p_seqedipedvenda*/,
       p_seqpedvendaitem,
       p_codacesso,
       p_seqproduto,
       0,             -- CONFIRMAR A CONDIÇÃO DE PAGAMENTO E AJUSTAR 
       p_qtdpedida,
       p_qtdembalagem,
       p_vlrembtabpreco,
       p_vlrembinformado,
       NULL);
      COMMIT;
END;

