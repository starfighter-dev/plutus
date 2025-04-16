<?php
   require('env.php');
   $conn_str = sprintf("mysql:host=%s;dbname=%s", DB_HOST, DB_NAME);
   $dbh = new PDO($conn_str, DB_USERNAME, DB_PASSWORD);
   $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   $dbh->exec("set names utf8"); // TODO bad?

   $sql = 'SELECT * FROM allowances WHERE startdate <= NOW() AND enddate >= NOW()';
   $stmt = $dbh->prepare($sql);
   $stmt->execute();
   while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      // If it's weekly, and a Friday or
      // if it's monthly and it's the 25th.
      if ( ( $row['frequency'] === 'weekly' && date('D') === 'Fri' ) || ( $row['frequency'] === 'monthly' && date('d') === '25' ) ) {
         $type = $row['frequency'] === 'weekly' ? 'Weekly' : 'Monthly';
         $sql2 = 'INSERT INTO transactions (account,type,date,descr,amount) VALUES (:account, :type, NOW(), :descr, :amount)';
         $stmt2 = $dbh->prepare($sql2);
         $stmt2->bindValue('account', $row['account'], PDO::PARAM_STR);
         $stmt2->bindValue('type', 'allowance', PDO::PARAM_STR);
         $stmt2->bindValue('descr', $type.' Allowance', PDO::PARAM_STR);
         $stmt2->bindValue('amount', $row['amount'], PDO::PARAM_STR);
         $stmt2->execute();
      }

   }

?>
