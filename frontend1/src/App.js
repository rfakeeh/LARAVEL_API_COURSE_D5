import './App.css';
import { Container, Col, Row } from 'react-bootstrap';
import Login from './Login';
import Menu from './Menu';
import Home from './Home';
import Dashboard from './Dashboard';
import { Routes, Route} from 'react-router-dom';
import ProtectedRoute from './ProtectedRoute';
import React, { useState } from 'react';
import NotFound from './NotFound';
import AddArticle from './AddArticle';

function App() {

  const [category, setCategory] = useState(null); // hook
  const [categories, setCategories] = useState([]);

  return (

    <>
      <Container fluid>
        <Row>
          <Col className='header'>
            <h1>News Platform Application</h1>
          </Col>
        </Row>

        <Row>
          <Col>
            <Menu  setCategory={setCategory} setCategories={setCategories} categories={categories}/>
          </Col>
        </Row>

        <Row>
          <Col className='content'>
              <Routes>
                <Route path='/' element={<Home category={category} />} />
                
                <Route path='/login' element={<Login />} />

                <Route path='/dash' element={<ProtectedRoute />}>
                  <Route exact path='/dash' element={<Dashboard />}/>
                </Route>

                <Route path='/add' element={<ProtectedRoute />}>
                  <Route exact path='/add' element={<AddArticle categories={categories} />}/>
                </Route>

                <Route path='*' element={<NotFound />} />

              </Routes>  
          </Col>
        </Row>

        <Row>
          <Col className='footer'>
            <span>Copyright reserved to <a href="mailto:rana.fakeeh@hotmail.com">rana.fakeeh@hotmail.com</a></span>
          </Col>
        </Row>

      </Container>

    </>
 
  );
}

export default App;
