<?php
   require('env.php');
   $today = (int)date('Ymd');
   // ^ This isn't used now, but it's for logic where < 20230101 etc.

   $run_for = date('Y-m-d');
   if ( isset($argv) && isset($argv[1]) ) {
      $run_for = $argv[1];
   }

   $interest_rate = 0.20; // 20% but compounding .. this was base rate + 16.5

   require 'vendor/autoload.php';

   $conn_str = sprintf("mysql:host=%s;dbname=%s", DB_HOST, DB_NAME);
   $dbh = new PDO($conn_str, DB_USERNAME, DB_PASSWORD);
   $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   $dbh->exec("set names utf8"); // TODO bad?

   // Check that we haven't already run for this day
   $sql = 'SELECT id FROM interest WHERE date=:run_for LIMIT 1';
   $stmt = $dbh->prepare($sql);
   $stmt->bindValue('run_for', $run_for, PDO::PARAM_STR);
   $stmt->execute();
   $row = $stmt->fetch(PDO::FETCH_ASSOC);
   if ( $row ) {
      die('data already exists for '.$run_for."\n");
   }

   $interest_accounts = [
      'person2' => [
         'email' => 'email@email.org.uk'
      ],
      'person3'  => [
         'email' => false
      ]
   ];

   // Every day we'll work out interest based on the current amount in
   // the account.

   $days_in_year = date('z', strtotime(date('Y').'-12-31'))+1;

   // The interest is balance * rate / days_in_year

   // If today is the first day of a month, we're going to figure out
   // all the interest for last month...
   if ( date('d') === '01' ) {
      $dt = new DateTime($run_for);
      $dt->sub(new DateInterval('P1D'));
      $last_month = $dt->format('Y-m-%');

      foreach ( $interest_accounts as $account => $v ) {
         $sql = 'SELECT * FROM interest WHERE account=:account AND date LIKE :date ORDER BY date ASC';
         $stmt = $dbh->prepare($sql);
         $stmt->bindValue('account', $account, PDO::PARAM_STR);
         $stmt->bindValue('date', $last_month, PDO::PARAM_STR);
         $stmt->execute();

         $balance = 0;
         while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
            $balance += (float)$row['interest'];
         }

         if ( $balance > 0 ) {
            $balance = sprintf("%.2f", $balance);
            $sql = 'INSERT INTO transactions (account,type,date,descr,amount) VALUES (:account,"interest",NOW(),:descr,:amount)';
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue('account', $account, PDO::PARAM_STR);
            $stmt->bindValue('descr', 'Interest payment', PDO::PARAM_STR);
            $stmt->bindValue('amount', $balance, PDO::PARAM_STR);
            $stmt->execute();
         }
      }
   }

   // Iterate the accounts and work out interest for today
   // Get a total for each account we're parsing.
   foreach ( $interest_accounts as $account => $v ) {
      $transactions = [];
      $sql = 'SELECT FORMAT(amount,2) as amount FROM transactions WHERE account=:account AND date <= :run_for ORDER BY id ASC';
      $stmt = $dbh->prepare($sql);
      $stmt->bindValue('account', $account, PDO::PARAM_STR);
      $stmt->bindValue('run_for', $run_for, PDO::PARAM_STR);
      $stmt->execute();

      $balance = 0.00;
      while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
           $row['amount'] = str_replace(',','',$row['amount']);
         $balance = bcadd($balance, $row['amount'], 2);
      }
      $interest_accounts[$account]['balance'] = $balance; // debug

      $interest_for_today = 0.00;

      if ( $balance > 0.00 ) {
         if ( $balance > 1500 ) {
            $balance = 1500.00;
         }
         $interest_for_today = ( $balance * $interest_rate ) / $days_in_year;
         $interest_accounts[$account]['interest_today'] = $interest_for_today;
         // ^ debug
      }

      $sql = 'INSERT INTO interest (account, date, rate, interest) VALUES (:account,:date,:rate,:interest)';
      $stmt = $dbh->prepare($sql);
      $stmt->bindValue('account', $account, PDO::PARAM_STR);
      $stmt->bindValue('date', $run_for, PDO::PARAM_STR);
      $stmt->bindValue('rate', sprintf("%.2f%%",($interest_rate*100)), PDO::PARAM_STR);
      $stmt->bindValue('interest', $interest_for_today, PDO::PARAM_STR);
      $stmt->execute();
   }

?>
