<?php
   require('env.php');
   require 'vendor/autoload.php';

   use Symfony\Component\Mailer\Transport;
   use Symfony\Component\Mailer\Mailer;
   use Symfony\Component\Mime\Email;
   use Symfony\Component\Mime\Address;

   $conn_str = sprintf("mysql:host=%s;dbname=%s", DB_HOST, DB_NAME);
   $dbh = new PDO($conn_str, DB_USERNAME, DB_PASSWORD);
   $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   $dbh->exec("set names utf8"); // TODO bad?

   $emails = [
      'account1' => 'anemail@test.com',
      'account2' => 'anemail@test.com',
   ];

   $to_parse = [];

   $sql = 'SELECT DISTINCT account FROM transactions ORDER BY account ASC';
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      if ( isset($emails[$row['account']]) ) {
         $to_parse[] = $row['account'];
      }
   }

   // Get a total for each account we're parsing.
   foreach ( $to_parse as $account ) {
      if ( !isset($emails[$account]) ) {
         continue; // no email
      }

      $transactions = [];
      $sql = 'SELECT FORMAT(amount,2) as amount FROM transactions WHERE account=:account ORDER BY id ASC';
      $stmt = $dbh->prepare($sql);
      $stmt->bindValue('account', $account, PDO::PARAM_STR);
      $stmt->execute();

      $balance = 0.00;
      while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
         $row['amount'] = str_replace(',','',$row['amount']);
         $balance = bcadd($balance, $row['amount'], 2);
      }
      if ( $account = 'martin') {
$balance = -100;
      }
      if ( $balance < 0 ) {

         $DSN = sprintf("smtp://%s:%s@%s:%s", 'apikey', SENDGRID_API_KEY, 'smtp.sendgrid.net', 25);
         $transport = Transport::fromDsn($DSN);
         $mailer = new Mailer($transport);

         $balance = sprintf('%.2f', abs($balance));

         $html= <<<HTML
<h3>Bank of Martin</h3>
<p>You are currently in debt to BoM. Our records show that you currently owe: <strong><font style="font-size:14pt;color:red">${balance}</font></strong></p>
<p>Arrangements should be made to pay this amount at your earliest convenience, so as to avoid undesirable actions such as a cricket bat to the knee.</p>
<p>View your account online at <a href="https://www.starfighter.dev/plutus/${account}">https://www.starfighter.dev/plutus/${account}</a>
<p style="font-size:8pt;color:#c0c0c0;">Please do not respond to this email, we do not view responses. If you object to receiving constant alerts about your debt, you can bugger off. We reserve the right to annoy you at will until all debts are paid.</p>
HTML;

         $text= <<<TEXT
Bank of Martin

You are currently in debt to BoM. Our records show that you currently owe: ${balance}

Arrangements should be made to pay this amount at your earliest convenience, so as to avoid undesirable actions such as a cricket bat to the knee.

View your account online at https://www.starfighter.dev/plutus/${account}

Please do not respond to this email, we do not view responses. If you object to receiving constant alerts about your debt, you can bugger off. We reserve the right to annoy you at will until all debts are paid.
TEXT;

         $email = (new Email())
            ->from( new Address( 'noreply@starfighter.dev', 'BoM' ))
            ->to(...[ $emails[$account] ])
            ->subject('BoM Debt Notification')
            ->text($text)
            ->html($html);

         // Send the message
         $mailer->send($email);
      }
   }

?>
