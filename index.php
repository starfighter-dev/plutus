<?php
   require('env.php');
   require 'vendor/autoload.php';
   session_start();
   $arr = explode('/', $_SERVER['REQUEST_URI']);
   $account = $arr[count($arr)-1];
   if ( $account === '' ) {
      $account = 'admin';
   }
   if ( !preg_match('/^(names|of|your|accounts)$/', $account)) {
      header('Location: https://www.google.com/');
      exit;
   }

   $smarty = new Smarty;

   $conn_str = sprintf("mysql:host=%s;dbname=%s", DB_HOST, DB_NAME);
   $dbh = new PDO($conn_str, DB_USERNAME, DB_PASSWORD);
   $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   $dbh->exec("set names utf8"); // TODO bad?

   if ( $account === 'admin' ) {
      if ( isset($_POST['password']) ) {
         if ( $_POST['username'] === 'admin' && $_POST['password'] === 'donkeyrules' ) {
            $_SESSION['logged_in'] = 1;
         }
      }

      if ( isset($_SESSION['logged_in']) ) {
         if ( isset($_POST['account']) ) {
            $account2 = $_POST['account'];
            $amount   = $_POST['amount'];
            $descr    = $_POST['descr'];

            $sql = 'INSERT INTO transactions (account,type,date,descr,amount) VALUES (:account, :type, NOW(), :descr, :amount)';
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue('account', $account2, PDO::PARAM_STR);
            $stmt->bindValue('type', 'web', PDO::PARAM_STR);
            $stmt->bindValue('descr', $descr, PDO::PARAM_STR);
            $stmt->bindValue('amount', $amount, PDO::PARAM_STR);
            $stmt->execute();
            $smarty->assign('success', "YEP");
         } else {
         }
         $smarty->assign('account', ucfirst($account));
         $smarty->display("view/admin.tpl");
         exit;
      }
      $smarty->display("view/login.tpl");
      exit;
   }

   // Load any allowance data for this account
   $allowance = 0;
   $allowance_frequency = 'weekly';
   $allowance_data = [];
   $sql = 'SELECT * FROM allowances WHERE account=:account';
   $stmt = $dbh->prepare($sql);
   $stmt->bindValue('account', $account, PDO::PARAM_STR);
   $stmt->execute();
   $now = new DateTime();
   $starting_date = 0;
   while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      $allowance_data[$row['id']] = $row;
      $start = new DateTime(sprintf("%s 00:00:00", $row['startdate']));
      $end   = new DateTime(sprintf("%s 23:59:59", $row['enddate']));
      if ( !$allowance && $now < $end && $now > $start ) {
         $allowance = $row['amount'];
         $allowance_frequency = $row['frequency'];
      }
      $allowance_data[$row['id']]['startdt'] = $start;
      $allowance_data[$row['id']]['enddt'] = $end;

      if ( !$starting_date || $start < $starting_date ) {
         $starting_date = $start;
      }
   }

   $transactions = [];
   $sql = 'SELECT * FROM transactions WHERE account=:account ORDER BY id ASC';
   $stmt = $dbh->prepare($sql);
   $stmt->bindValue('account', $account, PDO::PARAM_STR);
   $stmt->execute();

   $balance = 0.00;
   $total_income = 0.00;
   $total_spending = 0.00;
   while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      $balance += (float)$row['amount'];
      $row['balance'] = sprintf("%.2f", $balance);
      $transactions[] = $row;

      if ( (float)$row['amount'] > 0 ) {
         $total_income += (float)$row['amount'];
      } else {
         $total_spending += abs((float)$row['amount']);
      }
   }

   $transactions = array_reverse( $transactions );
   $transactions = array_slice($transactions, 0, 49); // Limit amount of data
   // Always display 5, but if there are more, see if they're older than 3 months...

   if ( count($transactions) > 20 ) {
      $final_transactions = [];
      $three_months_ago = new DateTime();
      $three_months_ago->sub(new DateInterval('P3M'));
      foreach ( $transactions as $t ) {
         $tdate = new DateTime(sprintf("%s 00:00:00", $t['date']));
         if ( $tdate < $three_months_ago ) {
            continue;
         }
         $final_transactions[] = $t;
      }
      $transactions = $final_transactions;
   }


   $smarty->assign('total_income', $total_income);
   $smarty->assign('total_spending', $total_spending);
   $smarty->assign('allowance', $allowance);
   $smarty->assign('allowance_frequency', $allowance_frequency);

   $now  = new DateTime();
   $then = new DateTime('2019-07-19 00:00:00');
   if ( $now > $then && $account !== 'alex' ) {
      $smarty->assign('currency', '£');
   } else {
      $smarty->assign('currency', '$');
   }

   $smarty->assign('balance', sprintf("%.2f", $balance));
   $smarty->assign('transactions', $transactions);
   $smarty->assign('account', ucfirst($account));
   $smarty->display("view/home.tpl");
?>
