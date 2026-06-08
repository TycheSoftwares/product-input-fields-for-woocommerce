/**
 * WordPress dependencies.
 */
import { 
    Card, 
    __experimentalVStack as VStack, 
    __experimentalHStack as HStack, 
    __experimentalHeading as Heading, 
    __experimentalText as Text, 
    ExternalLink,
    CardHeader,  
    CardBody,
    CardFooter,
    Spinner
} from '@wordpress/components';

import { useRef, useEffect, useState } from "@wordpress/element";
import { __ } from '@wordpress/i18n';
import { General, FieldBuilder } from './screens';
import { getSettings, getFields } from './data/api';

/**
 * External dependencies
 */
import { Navigate, Route, Routes, NavLink } from 'react-router-dom';

function App() {
    const parentRef = useRef(null);

    const [data, setData] = useState(null);
    const [isLoading, setIsLoading] = useState(true);

    useEffect(() => {

        async function loadAll() {

            try {
                const [general, fields] = await Promise.all([
                    getSettings(),
                    getFields()
                ]);

                setData({
                    general,
                    fields
                });
            } finally {
                setIsLoading(false);
            }
        }

        loadAll();

    }, []);

    if (isLoading || !data) {
        return <Spinner />;
    }

    // Filter tabs based on license status outside the return statement
    const tabs = [
        {
            name: 'general',
            title: __('General', 'woocommerce-flexi-bogo'),
            path: '/'
        },
        {
            name: 'fields',
            title: __('Field Builder', 'woocommerce-flexi-bogo'),
            path: '/fields'
        }
    ];

    return (
        <Card ref={parentRef}>
            <CardHeader>
                <VStack>
                    <Heading level={ 4 }>Product Input Fields for WooCommerce</Heading>
                    <Text>{__( 'Easily add customizable input fields to your WooCommerce products.', 'product-input-fields-for-woocommerce')}</Text>
                </VStack>
            </CardHeader>
            <CardBody style={{paddingTop: '0px'}}>
                <VStack>
                    <HStack style={{borderBottom: '1px solid #e5e5e5' }}>
                        <div className="header-dashboard-tabs">

                            {tabs.map((tab) => (
                                <NavLink
                                    key={tab.name}
                                    to={tab.path}
                                    className={({ isActive }) =>
                                        "dashboard-tab" + (isActive ? " is-active" : "")
                                    }
                                >
                                    {tab.title}
                                </NavLink>
                            ))}

                        </div>
                    </HStack>

                    <Routes>
                        <Route path='/' element={<General parentRef={parentRef} settingsData={data?.general || null} />}></Route>
                        <Route path='/fields' element={<FieldBuilder parentRef={parentRef} settingsData={data?.fields || null} />}></Route>
                        <Route path='*' element={<Navigate to={'/'} replace />}></Route>
                    </Routes>
                    
                </VStack>
            </CardBody>
            <CardFooter justify='center'>
                <VStack style={{ padding: "20px 0" }}>
                    <HStack justify="center" style={{ marginBottom: "22px" }}>
                        <ExternalLink href="https://support.tychesoftwares.com/help/2285384554/" className="bogo-link">
                        Need support?
                        </ExternalLink>
                        <Text style={{ fontWeight: "bold" }}>
                        We’re always happy to help you.
                        </Text>
                    </HStack>
                    <HStack justify="center">
                        <Text>If this plugin helped you,</Text>
                        <ExternalLink href="https://wordpress.org/support/plugin/product-input-fields-for-woocommerce/reviews" className="bogo-link">
                        please rate it
                        </ExternalLink>
                        <Text style={{ fontSize: "17px", color: "#FFBA00" }}>★★★★★</Text>
                    </HStack>
                </VStack>
            </CardFooter>
        </Card>
    );
}

export default App;