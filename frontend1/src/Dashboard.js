import React, { useEffect, useState } from 'react';
import { Row, Col, Image, Nav, Button, Badge, Table } from 'react-bootstrap';
import axios from 'axios';
import Cookies from 'universal-cookie';
import { useNavigate } from 'react-router-dom';



const cookies = new Cookies();

export default function Dashboard() {

    const token = cookies.get('token');

    const [articles, setArticles] = useState([]);
    const [user, setUser] = useState({
        name: 'Anonymous',
        avatar: 'https://placehold.co/400x400',
        total: 0,
        public: 0,
        private: 0,
        finished: 0,      
    });
    
    useEffect(() => {
        let config = {
            method: 'get',
            url: 'http://127.0.0.1:8000/api/user/',
            headers: {
              Authorization: `Bearer ${token}`,
            },
          };

        axios(config)
          .then((result) => {
            let user = result.data.data;
            console.log(user);
            setUser({
                name: user.name,
                avatar: 'http://127.0.0.1:8000/storage/'+user.avatar,
                total: user.total_news_count,
                public: user.public_news_count,
                private: user.private_news_count,
                finished: user.completed_news_count,      
            });
          })
          .catch((error) => {
            console.log(error.response);
          });

          config = {
            method: 'get',
            url: 'http://127.0.0.1:8000/api/user/news',
            headers: {
              Authorization: `Bearer ${token}`,
            },
          };

        axios(config)
          .then((result) => {
            let articles = result.data.data.data;
            console.log(articles);
            setArticles(articles);
          })
          .catch((error) => {
            console.log(error.response);
          });

      }, []);

      const navigate = useNavigate();

      const logout = () => {
        // destroy the cookie
        cookies.remove('token', { path: '/' });
        navigate('/');
      }


    return (
        <>
            <Row>
                <Col lg={2} className='side-bar'>
                    <Row>
                        <Col><Image src={user.avatar} roundedCircle fluid /></Col>
                    </Row>
                    <Row>
                        <Col><p>{user.name}</p></Col>
                    </Row>
                    <Row>
                        <Col>
                            <Nav className='flex-column'>
                                <Nav.Link href='/dash'>Dashboard</Nav.Link>
                                <Nav.Link href='/add'>Add Article</Nav.Link>
                                <Nav.Link onClick={() => logout()}>Logout</Nav.Link>
                            </Nav>
                        </Col>
                    </Row>
                </Col>
                <Col>
                    <Row>
                        <Col>
                            <h2>Dashboard 1</h2>
                            <br/>
                        </Col>
                    </Row>
                    <Row>
                        <Col>
                            <Button variant='outline-danger' size='lg'>
                                Total &nbsp;&nbsp;<Badge bg='secondary'>{user.total}</Badge>
                            </Button>
                        </Col>
                        <Col>
                            <Button variant='outline-warning' size='lg'>
                                Public &nbsp;&nbsp;<Badge bg='secondary'>{user.public}</Badge>
                            </Button>
                        </Col>
                        <Col>
                            <Button variant='outline-success' size='lg'>
                                Private &nbsp;<Badge bg='secondary'>{user.private}</Badge>
                            </Button>
                        </Col>
                        <Col>
                            <Button variant='outline-info' size='lg'>
                                Finished <Badge bg='secondary'>{user.finished}</Badge>
                            </Button>
                        </Col>
                    </Row>
                    <Row className='justify-content-center'>
                        <Col md={8}>
                            <br/><br/>
                            <Table striped>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Visible</th>
                                        <th>Completed</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {
                                        articles.map((item, idx) => (
                                            <tr key={item.id}>
                                                <td>{idx+1}</td>
                                                <td>{item.title}</td>
                                                <td>{item.visible}</td>
                                                <td>{item.completed}</td>
                                                <td>Update | Delete</td>
                                            </tr>
                                        ))
                                    }
                                </tbody>
                            </Table>
                        </Col>
                    </Row>
                </Col>
            </Row>
        </>
    );
}