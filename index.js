const express = require('express');

const app = express();

app.use('/', express.static('app/dist'));

app.listen(3000);
