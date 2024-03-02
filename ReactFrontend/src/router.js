import { createBrowserRouter } from 'react-router-dom';
import Signup from './view/Signup';
import NotFound from './view/NotFound';
import Login from './view/Login';

const route = createBrowserRouter([
    {
        path: '/login',
        element: () => <Login />
    },
    {
        path: '/signup',
        element: () => <Signup />
    },
    {
        path: '*',
        element: () => <NotFound />
    },
]);
