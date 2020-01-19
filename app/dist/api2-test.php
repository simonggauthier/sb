<!DOCTYPE html>
<html>
    <body>
    	<form method="post" action="/sbapi/login">
			<input type="text" name="username" placeholder="username">
			<input type="password" name="password" placeholder="password">
			<input type="hidden" name="device" value="desktop">
			<input type="submit" value="Login">
        </form>

    	<form method="post" action="/sbapi/login-by-token">
			<input type="text" name="tokenId" placeholder="Token ID">
			<input type="submit" value="Login">
        </form>

        <form method="get" action="/sbapi/convert">
			<input type="submit" value="convert">
        </form>

        <form method="post" action="/sbapi/category">
            <input type="text" name="name" placeholder="Name">
            <input type="text" name="color" placeholder="Color">
            <input type="text" name="bookId" value="3">
            <input type="submit" value="Create category">
        </form>

        <form method="post" action="/sbapi/category">
            <input type="text" name="id" value="305">
            <input type="text" name="name" placeholder="Name">
            <input type="text" name="color" placeholder="Color">
            <input type="submit" value="Update category">
        </form>

        <form method="post" action="/sbapi/transaction">
            <input type="text" name="bookId" value="3">
            <input type="text" name="direction" placeholder="Direction">
            <input type="text" name="title" placeholder="Title">
            <input type="text" name="categoryId" placeholder="categoryId">
            <input type="text" name="amount" placeholder="Amount">
            <input type="text" name="date" placeholder="date">
            <input type="submit" value="Create transaction">
        </form>

        <form method="post" action="/sbapi/transaction">
            <input type="text" name="id" value="1499">
            <input type="text" name="direction" placeholder="Direction">
            <input type="text" name="title" placeholder="Title">
            <input type="text" name="categoryId" placeholder="categoryId">
            <input type="text" name="amount" placeholder="Amount">
            <input type="text" name="date" placeholder="date">
            <input type="submit" value="Update transaction">
        </form>

        <form method="post" action="/sbapi/transaction">
            <input type="text" name="id" value="1450">
            <input type="hidden" name="delete">
            <input type="submit" value="Delete transaction">
        </form>
    </body>1572580800000
</html>
