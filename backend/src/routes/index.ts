// Codice di ChatGPT
import { Router } from 'express';
//import userRoutes from './auth.routes';
import testRoutes from "./test.routes"

const router = Router();

router.use('/hi', testRoutes);
//router.use('/auth', userRoutes);

export default router;
