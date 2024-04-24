<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script defer src="/public/script.js"></script>
</head>
<body>
<div>
    <form action="/api/add/contact" method="POST" id="addContactForm">
        <label for="name">Contact Name: </label>
        <input type="text" name="name" id="name">
        <label for="phone_number">Phone Number: </label>
        <input type="text" name="phone_number" id="phone_number">
        <input type="submit" value="Add New Contact">
    </form>
</div>
<div id="error-msg" style="color: red"></div>
<br>
<table>
    <thead>
    <tr>
        <?php foreach ($data["columns"] as $column): ?>
            <th><?= $column ?></th>
        <?php endforeach; ?>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($data["contacts"] as $contact): ?>
        <tr data-contactid="<?= $contact[0] ?>">
            <?php foreach ($contact as $value): ?>
                <th><?= $value ?></th>
            <?php endforeach; ?>
            <th>
                <button onclick="deleteContact(<?= $contact[0] ?>)">Delete</button>
            </th>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>