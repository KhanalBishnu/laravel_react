import React from 'react'
import { Link, Route, Routes } from 'react-router-dom'
import Home from './../pages/Home';
import Create from './../pages/Create';
import Dashboard from './../pages/Dashboard';
import NotFound from './NotFound';
import AuthUser from '../AuthUser';
import UserList from '../pages/UserList';

function Auth() {
    const {token,logout}=AuthUser();
    const handleLogout=()=>{
        if(token !=undefined){
            logout();
        }
    }
  return (
    <>
     <nav className="navbar navbar-expand-lg navbar-dark bg-dark">
        <div className="container-fluid">
          <Link className="navbar-brand" to={'/'}>
            Dashboard
          </Link>
          <button
            className="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent"
            aria-expanded="false"
            aria-label="Toggle navigation"
          >
            <span className="navbar-toggler-icon"></span>
          </button>
          <div className="collapse navbar-collapse" id="navbarSupportedContent">
            <ul className="navbar-nav me-auto mb-2 mb-lg-0">
             
              <li className="nav-item">
                <Link className="nav-link" to={'/create'}>
                  Create User
                </Link>
              </li>
              <li className="nav-item">
                <Link className="nav-link" to={'/Users'}>
                  View Users
                </Link>
              </li>
             
            </ul>
            <form className="d-flex">
                <span className="nav-link text-white mx-4  m-auto " onClick={handleLogout}>
                  Logout
                </span>
              <input
                className="form-control me-2"
                type="search"
                placeholder="Search"
                aria-label="Search"
              />
              <button className="btn btn-success" type="submit">
                Search
              </button>
            </form>
          </div>
        </div>
      </nav>
      <div className="container">
      {/* <ContextProvider> */}
      <Routes>

          <Route path="/" element={<Dashboard />} />
          <Route path="create" element={<Create />} />
          <Route path="/dashboard" element={<Dashboard />} />
          <Route path="/users" element={<UserList />} />
        <Route path="*" element={<NotFound />} />
      </Routes>
      {/* </ContextProvider> */}
      </div>
    </>
  )
}

export default Auth