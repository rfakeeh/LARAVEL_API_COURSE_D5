import React from 'react';
import { Navigate, Outlet } from 'react-router-dom';
import Cookies from 'universal-cookie';

const cookies = new Cookies();

export default function ProtectedRoute() {

    const token = cookies.get('token');

    return token ? (<Outlet /> ) : (<Navigate to='/login' />) ;

  }