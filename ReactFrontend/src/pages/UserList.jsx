import React, { useEffect, useState } from 'react'
import AuthUser from '../AuthUser';

function UserList() {
    const [users,setUsers]=useState([]);
    const {http}=AuthUser();
    useEffect(()=>{
        getAllUser();
    },[]);
    const getAllUser=()=>{
        http.get('/users').then((res)=>{
            if(res.data.response){
                setUsers(res.data.users);
            }
        });
    }
  return (
    <div className='container'>
        <div className="row">
            <h6>Users List </h6>
            <table  className='table table-bordered '>
                <thead>
                    <tr>
                        <th>SN</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
               { users.map((user,index) => (
                    <tr key={user.id}>
                        <td>{++index}</td>
                        <td>{user.name}</td>
                        <td>{user.email}</td>
                        <td>
                            <button className="btn btn-primary mx-1">Edit</button>
                            <button className="btn btn-danger">Delete</button>
                        </td>
                    </tr>
                ))};

                </tbody>
            </table>
        </div>
    </div>
  )
}

export default UserList