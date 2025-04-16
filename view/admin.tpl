{include "view/header.tpl" page="history"}
    <div class="container theme-showcase" role="main">

      <!-- Main jumbotron for a primary marketing message or call to action -->
      <div class="jumbotron">
        <h1>{$account}</h1>
      </div>

{if isset($success)}
<div class="alert alert-success" role="alert">
  {$success}
</div>
{/if}
{if isset($error)}
<div class="alert alert-danger" role="alert">
   {$error}
</div>
{/if}

<div class="bd-example" data-example-id="">
<form method="POST">
  <div class="form-group">
    <label for="account">Account</label>
    <select class="form-control" id="account" name="account">
      {foreach from=$ACCOUNTS item=$v}
      <option value="{$v}">{$v|ucfirst}</option>
      {/foreach}
    </select>
  </div>
  <div class="form-group">
    <label for="descr">Description</label>
    <input type="text" class="form-control" name="descr" id="descr" />
    <br/>
    <button data-like="Transfer" class="setdescr btn btn-secondary btn-sm" type="button">Transfer</a>
    <button data-like="Card payment" class="setdescr btn btn-secondary btn-sm" type="button">Card Payment</a>
  </div>
  <div class="form-group">
    <label for="amount">Amount</label>
    <input type="text" class="form-control" name="amount" id="amount" placeholder="-5.00" />
  </div>
  <button type="submit" class="btn btn-primary">Submit</button>
</form>
</div>


    </div> <!-- /container -->
{include "view/footer.tpl"}
