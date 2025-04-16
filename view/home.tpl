{include "view/header.tpl" page="history"}
    <div class="container theme-showcase" role="main">

      <!-- Main jumbotron for a primary marketing message or call to action -->
      <div class="jumbotron">
        <h1 class="display-4">{$account}</h1>
        {if $balance < 0}
           <p class="lead" style="color:red;">You are {$currency}{$balance} in debt to BOM.</p>
        {else}
           <p class="lead">{$currency}{$balance} is available.</p>
         {/if}
      </div>

      {if $account !== 'Martin' && $account !== 'Morgan'}
      <div class="row">
         <div class="col-sm-12">
            <div class="panel panel-info">
               <div class="panel-heading">
               <h3 class="panel-title">Totals</h3>
               </div>
               <div class="panel-body">
                  Total income: {$currency}{$total_income}, Total spent: {$currency}{$total_spending}
               </div>
            </div>
         </div><!-- /.col-sm-4 -->
      </div>
      {/if}

      {if $allowance}
         <div class="row">
            <div class="col-sm-12">
               <div class="panel panel-success">
                  <div class="panel-heading">
                  <h3 class="panel-title">Allowance</h3>
                  </div>
                  <div class="panel-body">
                     You receive an allowance of {$currency}{$allowance} {if $allowance_frequency === 'monthly'}on the 25th of each month{else}every Friday, until your 18th birthday{/if}.
                  </div>
               </div>
            </div><!-- /.col-sm-4 -->
         </div>
      {/if}

      <div class="page-header">
        <h1>Recent</h1>
      </div>

      <div class="row">
        <div class="col-sm-12">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Date</th>
                <th>Item</th>
                <th>Amount</th>
                <th>Balance</th>
              </tr>
            </thead>
            <tbody>
              {foreach from=$transactions item=i}
              <tr>
                <td>{$i.date}</td>
                <td>{$i.descr}</td>
                <td>{$i.amount|replace:'-':''}</td>
                <td>{$i.balance}</td>
              </tr>
              {/foreach}
            </tbody>
          </table>
        </div>
      </div>

    </div> <!-- /container -->
{include "view/footer.tpl"}
