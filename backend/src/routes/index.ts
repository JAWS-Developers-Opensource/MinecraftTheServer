// Codice di ChatGPT
import { Router } from 'express';
import auth from "../routes/auth.routes";

const router = Router();

router.get('/hi', (req, res) => {
    res.json({ status: "up", version: "0" })
});

router.use('/auth', auth);

export default router;
