import React, { useEffect, useState } from 'react';
import { Nav, NavDropdown } from 'react-bootstrap';
import axios from 'axios';
import { useLocation, useNavigate } from 'react-router-dom';

export default function Menu({ setCategory, setCategories, categories }) {

    const location = useLocation();
    const currentPath = location.pathname;
    const navigate = useNavigate();
    

    //const [categories, setCategories] = useState([]);

    useEffect(() => {
        
        const config = {
          method: 'get',
          url: 'http://127.0.0.1:8000/api/category',
        };

        axios(config)
          .then((result) => {
            console.log(result);
            setCategories(result.data.data.data);
          })
          .catch((error) => {
            console.log(error.response);
          });

      }, []);

      const handleClick = (category) => {
        setCategory(category);
        if (currentPath !== '/') {
            navigate('/');
        }
      }

    return (
        <>
            <Nav variant='tabs' className='justify-content-end'>
                <Nav.Item>
                    <Nav.Link href='/'>Home</Nav.Link>
                </Nav.Item>

                <NavDropdown title='Category' id='navDropdown'>

                    {categories.map((item, index) => (
                        <NavDropdown.Item key={index} onClick={() => handleClick(item)} >{item.name} ({item.news_count})</NavDropdown.Item>
                    ))}

                    <NavDropdown.Divider />
                    <NavDropdown.Item href='/'>All Categories</NavDropdown.Item>
                </NavDropdown>
                
                <Nav.Item>
                    <Nav.Link href='/login'>Login</Nav.Link>
                </Nav.Item>
            </Nav>

        </>
    )
}