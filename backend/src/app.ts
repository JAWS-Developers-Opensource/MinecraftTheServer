const express = require('express');
const cors = require('cors');
const routes = require('./routes/index');

const backend = express();

backend.use(cors());
backend.use(express.json());

backend.use('/v1', routes);

module.exports = backend;
