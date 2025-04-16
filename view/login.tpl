{include "view/header.tpl" page="history"}
<link href="css/signin.css" rel="stylesheet" />
<div class="container text-center theme-showcase" role="main">
    <form method="POST" class="form-signin">
      <h1 class="h3 mb-3 font-weight-normal">Please sign in</h1>
      <label for="inputUser" class="sr-only">Username</label>
      <input type="text" id="inputUser" name="username" class="form-control" placeholder="Username" required autofocus>
      <label for="inputPassword" class="sr-only">Password</label>
      <input type="password" id="inputPassword" name="password" class="form-control" placeholder="Password" required>
      <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
    </form>
</div>
{include "view/footer.tpl"}
