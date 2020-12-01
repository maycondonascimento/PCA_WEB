<?php
header('Content-Type: text/html; charset=utf-8');


$email_from = "contato@webnatico.com.br"; //. $_SERVER[HTTP_HOST];

if( PATH_SEPARATOR ==';'){ $quebra_linha="\r\n";

} elseif (PATH_SEPARATOR==':'){ $quebra_linha="\n";

} elseif ( PATH_SEPARATOR!=';' and PATH_SEPARATOR!=':' )  {echo ('Esse script não funcionará corretamente neste servidor, a função PATH_SEPARATOR não retornou o parâmetro esperado.');

}

//pego os dados enviados pelo formulário 
$nome_para = $_POST["nome_para"];
$email = $_POST["email"];
$mensagem = $_POST["mensagem"];
$compassunto = "Contato Site - ";
$assunto = "Contato Site - ";
$assunto .= $_POST["assunto"];
//formato o campo da mensagem 
$mensagem = wordwrap( $mensagem, 50, "<br>", 1);



// Cria uma variável que terá os dados do erro
$erro = false;

// Verifica se o POST tem algum valor
if ( !isset( $_POST ) || empty( $_POST ) ) {
    echo '<script type="text/JavaScript">
    alert("Nada foi postado, Por favor verifique!!");
    history.back();
          </script>
    ';
}

// Cria as variáveis dinamicamente
foreach ( $_POST as $chave => $valor ) {
    // Remove todas as tags HTML
    // Remove os espaços em branco do valor
    $$chave = trim( strip_tags( $valor ) );

    // Verifica se tem algum valor nulo
    if ( empty ( $valor ) ) {
        echo '<script type="text/JavaScript">
    alert("Existem campos em branco, Por favor verifique!");
    history.back();
          </script>
    ';
    }
}


// Verifica se $email realmente existe e se é um email.
// Também verifica se não existe nenhum erro anterior
if ( ( ! isset( $email ) || ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) && !$erro ) {
    echo '<script type="text/JavaScript">
    alert("Por favor, Digite um Email válido!");
    history.back();
          </script>
    ';

}



// Se existir algum erro, mostra o erro
if ( $erro ) {
    echo $erro;
} else {
    // Se a variável erro continuar com valor falso
    // Você pode fazer o que preferir aqui, por exemplo,
    // enviar para a base de dados, ou enviar um email
    // Tanto faz. Vou apenas exibir os dados na tela.


    $arquivo = isset($_FILES["arquivo"]) ? $_FILES["arquivo"] : FALSE;

    if (file_exists($arquivo["tmp_name"]) and !empty($arquivo)) {

        $fp = fopen($_FILES["arquivo"]["tmp_name"], "rb");
        $anexo = fread($fp, filesize($_FILES["arquivo"]["tmp_name"]));
        $anexo = base64_encode($anexo);

        fclose($fp);

        $anexo = chunk_split($anexo);


        $boundary = "XYZ-" . date("dmYis") . "-ZYX";

        $mens = "--$boundary" . $quebra_linha . "";
        $mens .= "Content-Transfer-Encoding: 8bits" . $quebra_linha . "";
        $mens .= "Content-Type: text/html; charset=UTF-8" . $quebra_linha . "" . $quebra_linha . ""; //plain
        $mens .= "$mensagem" . $quebra_linha . "";
        $mens .= "--$boundary" . $quebra_linha . "";
        $mens .= "Content-Type: " . $arquivo["type"] . "" . $quebra_linha . "";
        $mens .= "Content-Disposition: attachment; filename=\"" . $arquivo["name"] . "\"" . $quebra_linha . "";
        $mens .= "Content-Transfer-Encoding: base64" . $quebra_linha . "" . $quebra_linha . "";
        $mens .= "$anexo" . $quebra_linha . "";
        $mens .= "--$boundary--" . $quebra_linha . "";

        $headers = "MIME-Version: 1.0" . $quebra_linha . "";
        $headers .= "From: $email_from " . $quebra_linha . "";
        $headers .= "Return-Path: $email_from " . $quebra_linha . "";
        $headers .= "Content-type: multipart/mixed; boundary=\"$boundary\"" . $quebra_linha . "";
        $headers .= "$boundary" . $quebra_linha . "";


//envio o email com o anexo 
        mail($email, $assunto, $mens, $headers, "-r" . $email_from);

        echo '<script type="text/JavaScript">
    alert("Seu e-mail foi enviado com sucesso. Obrigado!");
    location.href="contato.html"
          </script>
    ';

    } //se nao tiver anexo
    else {

        $headers = "MIME-Version: 1.0" . $quebra_linha . "";
        $headers .= "Content-type: text/html; charset=UTF-8" . $quebra_linha . "";
        $headers .= "From: $email_from " . $quebra_linha . "";
        $headers .= "Return-Path: $email_from " . $quebra_linha . "";

//envia o email sem anexo 
        mail($email, $assunto, $mensagem, $headers, "-r" . $email_from);


        echo '<script type="text/JavaScript">
    alert("Seu e-mail foi enviado com sucesso. Obrigado!");
    location.href="contato.html"
          </script>
    ';
    }
}
?>