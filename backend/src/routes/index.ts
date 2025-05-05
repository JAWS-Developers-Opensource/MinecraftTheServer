const expres = require('express');
const userRoutes = require('./user.routes');

const router = expres.Router();

router.use('/users', userRoutes);

module.exports = router;