// Codice di ChatGPT
import { Request, Response } from 'express';
import User from '../models/User';

export const login = async (req: Request, res: Response): Promise<void> => {
    const users = await User.find();
    res.json(users);
};

export const createUser = async (req: Request, res: Response): Promise<void> => {
    const user = new User(req.body);
    await user.save();
    res.status(201).json(user);
};
