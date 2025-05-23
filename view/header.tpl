
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>Plutus</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap theme -->
    <link href="css/bootstrap-theme.min.css" rel="stylesheet">

  </head>

  <body>

    <!-- Fixed navbar -->
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Plutus</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li {if $page=='history'}class="active"{/if}><a href="{if isset($smarty.session.logged_in)}admin{else}#{/if}">{if isset($account)}{if isset($smarty.session.logged_in)}Admin{else}{$account}{/if}{else}Login{/if}</a></li>
          {if isset( $smarty.session.logged_in )}
          <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Accounts<span class="caret"></span></a>
              <ul class="dropdown-menu">
               {foreach from=$ACCOUNTS item=$v}
                <li><a href="{$v}">{$v|ucfirst}</a></li>
               {/foreach}
              </ul>
            </li>
           {/if}
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>
