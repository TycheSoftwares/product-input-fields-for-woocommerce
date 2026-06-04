import { useForm, Controller, set } from "react-hook-form";
import { 
    __experimentalVStack as VStack,
    __experimentalHStack as HStack,
    __experimentalHeading as Heading, 
    Button,
    ToggleControl,
    __experimentalNumberControl as NumberControl,
    __experimentalInputControl as InputControl,
    __experimentalConfirmDialog as ConfirmDialog,
    SelectControl,
    withNotices,
    Spinner
} from "@wordpress/components";

import { dispatch } from '@wordpress/data';
import { store as coreDataStore } from '@wordpress/core-data';

import { __ } from '@wordpress/i18n';
import { SettingsCardSection } from "../components";
import { useCallback, useState, useEffect, useRef } from "@wordpress/element";
import { getSettings, updateSettings } from "../data/api";


function General({ noticeOperations, noticeUI, parentRef, settingsData }) {
    const toBoolean = (value) => value === 'yes' || value === true;
    const [settings, setSettings] = useState([]);
    const [showLoader, setShowLoader] = useState(false);
    const [isDialogOpen, setIsDialogOpen] = useState(false);

    const { control, handleSubmit, reset, watch, setValue, getValues, unregister, formState: { isDirty } } = useForm({
        defaultValues: settingsData ?? {
            enabled: false
        },
    });
    const fillURLParams = toBoolean(watch('fill_frontend_url_parameter'));

    const onSubmit = async (data) => {
        await handleSettingsUpdate(data);
    };

    const onError = () => {
        noticeOperations.removeAllNotices();
        noticeOperations.createNotice({
            status: 'error',
            content: 'Error saving the settings.',
        });
    }

    /**
     * Fetch Rules using api and update in state
     */
    const fetchSettings = useCallback(async () => {
       // setShowLoader(true);
        try {
            const data = await getSettings();
            setSettings(data);
            reset(data);
        } catch (error) {
            console.error('Failed to fetch settings:', error);
        } finally {
          //  setShowLoader(false)
        }
    }, [settings, reset, setShowLoader]);

    useEffect(() => {
        fetchSettings();
    }, []);

    // Scroll up to the notice message when it appears
    useEffect(() => {
        if (noticeUI && parentRef?.current) {

            parentRef.current.scrollIntoView({
                behavior: "smooth",
                block: "start"
            });
    
            const timer = setTimeout(() => {
                noticeOperations.removeAllNotices();
            }, 4000); // 4 seconds
    
            return () => clearTimeout(timer);
        }
    }, [noticeUI]);

    const handleSettingsUpdate = async (data) => {  
        setShowLoader(true);

        try {
            const response = await updateSettings(data);

            if (response) {
                fetchSettings();
                noticeOperations.removeAllNotices();
                noticeOperations.createNotice({
                    status: 'success',
                    content: 'Settings saved successfully.',
                });
            } 
        } catch (error) {
            noticeOperations.removeAllNotices();
            noticeOperations.createNotice({
                status: 'error',
                content: error.message,
            });
        } finally {
            setShowLoader(false);
        }
    };

    const resetSettings = async () => {
        setShowLoader(true);
        try {
            const response = await updateSettings({
                enabled: false,
                local_enabled: false,
                frontend_position: 'woocommerce_before_add_to_cart_button',
                frontend_position_priority: '10',
                frontend_before: '<table id="alg-product-input-fields-table" class="alg-product-input-fields-table">',
                frontend_template: '<tr><td><label for="%field_id%">%title%</label></td><td>%field%</td></tr>',
                frontend_after: '</table>',
                frontend_order_table_format: '%title% %value%',
                frontend_refill: true,
                attach_to_admin_new_order: true,
                attach_to_customer_processing_order: true,
            });

            if (response) {
                fetchSettings();
                noticeOperations.removeAllNotices();
                noticeOperations.createNotice({
                    status: 'success',
                    content: 'Settings have been successfully reset to default values.',
                });
            } 
        } catch (error) {
            noticeOperations.removeAllNotices();
            noticeOperations.createNotice({
                status: 'error',
                content: error.message,
            });
        } finally {
            setShowLoader(false);
            setIsDialogOpen(false);
        }
    }

    const resetTracking = () => {
		setShowLoader(true);
		dispatch(coreDataStore)
			.saveEntityRecord('root', 'site', {
				pif_allow_tracking: '',
				ts_tracker_last_send: '',
			})
			.then(() => {
				noticeOperations.removeAllNotices();
				noticeOperations.createNotice({
					status: 'success',
					content: 'Tracking has been successfully reset.',
				});
			})
			.finally(() => {
				setShowLoader(false);
			});
	};

    return (
       <VStack style={{marginTop: '30px'}}>
            {noticeUI}
            <form onSubmit={handleSubmit(onSubmit, onError)}>
                <VStack className={'pif_setting_section'} spacing={10}>

                    <SettingsCardSection
                        heading={ __( 'Product Input Fields Options', 'product-input-fields-for-woocommerce' ) }
                        control={ control }
                        fields={ [
                            {
                                name: 'enabled',
                                defaultValue: false,
                                render: ( field ) => (
                                    <ToggleControl
                                        label={ __( 'Enable plugin', 'product-input-fields-for-woocommerce' ) }
                                        help={ __( 'WooCommerce Product Input Fields.', 'product-input-fields-for-woocommerce' ) }
                                        checked={ toBoolean(field.value) }
                                        onChange={ field.onChange }
                                    />
                                ),
                            },
                        ] }
                    />

                    <SettingsCardSection
                        heading={ __( 'Product Input Fields – Per Product', 'product-input-fields-for-woocommerce' ) }
                        subHeading={ __( 'When enabled, this option will add a "Product Input Fields" section to each product\'s Edit page, allowing you to add custom input fields on a per-product basis.', 'product-input-fields-for-woocommerce' ) }
                        control={ control }
                        fields={ [
                            {
                                name: 'local_enabled',
                                defaultValue: false,
                                label: __( 'Enable section', 'product-input-fields-for-woocommerce' ),
                                render: ( field ) => (
                                    <ToggleControl
                                        label={ __( 'Add custom input fields on a per-product basis', 'product-input-fields-for-woocommerce' ) }
                                        checked={ toBoolean(field.value) }
                                        onChange={ field.onChange }
                                        className='pif-no-margin'
                                    />
                                ),
                            },
                        ] }
                    />

                    <SettingsCardSection
                        heading={ __( 'Frontend Options', 'product-input-fields-for-woocommerce' ) }
                        control={ control }
                        fields={ [
                            {
                                name: 'frontend_position',
                                defaultValue: 'before_add_to_cart',
                                label: __( 'Position', 'product-input-fields-for-woocommerce' ),
                                render: ( field ) => (
                                    <SelectControl
                                            label={''}
                                            value={field.value}
                                            options={[
                                                { label: __('Before add to cart button', 'product-input-fields-for-woocommerce'), value: 'woocommerce_before_add_to_cart_button' },
                                                { label: __('After add to cart button', 'product-input-fields-for-woocommerce'), value: 'woocommerce_after_add_to_cart_button' },
                                                { label: __('Do not display', 'product-input-fields-for-woocommerce'), value: 'disable' },
                                            ]}
                                            onChange={(value) => {
                                                field.onChange(value);
                                            }}
                                            help={ __( 'If set to "Do not display", alternatively you can use [alg_display_product_input_fields] shortcode, or PHP alg_display_product_input_fields() function.', 'product-input-fields-for-woocommerce' ) }
                                            __nextHasNoMarginBottom
                                    />
                                ),
                            },
                            {
                                name: 'frontend_position_priority',
                                defaultValue: '10',
                                label: __( 'Position Priority', 'product-input-fields-for-woocommerce' ),
                                render: ( field ) => (
                                    <NumberControl
                                        help={ __( 'Ignored if Position is set to "Do not display".', 'product-input-fields-for-woocommerce' ) }
                                        value={ field.value }
                                        onChange={ field.onChange }
                                        defaultValue={10}
                                    />
                                ),
                            },
                            {
                                name: 'frontend_before',
                                defaultValue: '',
                                label: __( 'HTML to add before product input fields', 'product-input-fields-for-woocommerce' ),
                                render: ( field ) => (
                                    <InputControl
                                        value={ field.value }
                                        onChange={ field.onChange }
                                        help={ __( 'Displays the background of the product input field.', 'product-input-fields-for-woocommerce' ) }
                                    />
                                ),
                            },
                            {
                                name: 'frontend_template',
                                defaultValue: '',
                                label: __( 'Product Input Field Template', 'product-input-fields-for-woocommerce' ),
                                render: ( field ) => (
                                    <InputControl
                                        value={ field.value }
                                        onChange={ field.onChange }
                                        help={ __( 'Replaced values: %field_id%, %title%, %field%', 'product-input-fields-for-woocommerce' ) }
                                    />
                                ),
                            },
                            {
                                name: 'frontend_after',
                                defaultValue: '',
                                label: __( 'HTML to Add After Product Input Fields', 'product-input-fields-for-woocommerce' ),
                                render: ( field ) => (
                                    <InputControl
                                        value={ field.value }
                                        onChange={ field.onChange }
                                        help={ __( 'This HTML tag is used to close the tag in "HTML to Add Before Product Input Fields setting".', 'product-input-fields-for-woocommerce' ) }
                                    />
                                ),
                            },
                            {
                                name: 'frontend_order_table_format',
                                defaultValue: '',
                                label: __( 'Item Name Order Table Format', 'product-input-fields-for-woocommerce' ),
                                render: ( field ) => (
                                    <InputControl
                                        value={ field.value }
                                        onChange={ field.onChange }
                                        help={ __( 'Affects Thank You page, Emails etc.', 'product-input-fields-for-woocommerce' ) }
                                    />
                                ),
                            },
                        ] }
                    />

                    <SettingsCardSection
                        heading={ __( 'Advanced Options', 'product-input-fields-for-woocommerce' ) }
                        control={ control }
                        fields={ [
                            {
                                name: 'frontend_refill',
                                defaultValue: false,
                                label: __( 'Refill Fields with Previous Input', 'product-input-fields-for-woocommerce' ),
                                render: ( field ) => (
                                    <ToggleControl
                                        label={ __( 'Remember and auto-fill previously entered values.', 'product-input-fields-for-woocommerce' ) }
                                        // help={ __( 'Remember and auto-fill previously entered values.', 'product-input-fields-for-woocommerce' ) }
                                        checked={ toBoolean(field.value) }
                                        onChange={ field.onChange }
                                        className='pif-no-margin'
                                    />
                                ),
                            },
                        ] }
                    />

                    <SettingsCardSection
                        heading={ __( 'Email Options', 'product-input-fields-for-woocommerce' ) }
                        control={ control }
                        fields={ [
                            {
                                name: 'attach_to_admin_new_order',
                                defaultValue: false,
                                render: ( field ) => (
                                    <ToggleControl
                                        label={ __( 'Attach Files to Admin\'s New Order Emails', 'product-input-fields-for-woocommerce' ) }
                                        help={ __( 'Attach files to new order emails sent to administrators.', 'product-input-fields-for-woocommerce' ) }
                                        checked={ toBoolean(field.value) }
                                        onChange={ field.onChange }
                                    />
                                ),
                            },

                            {
                                name: 'attach_to_customer_processing_order',
                                defaultValue: false,
                                render: ( field ) => (
                                    <ToggleControl
                                        label={ __( 'Attach Files to Customer\'s Processing Order Emails', 'product-input-fields-for-woocommerce' ) }
                                        help={ __( 'Attach files to processing order emails sent to customers.', 'product-input-fields-for-woocommerce' ) }
                                        checked={ toBoolean(field.value) }
                                        onChange={ field.onChange }
                                    />
                                ),
                            },
                        ] }
                    />

                    <SettingsCardSection
                        heading={ __( 'Reset Section Settings', 'product-input-fields-for-woocommerce' ) }
                        control={ control }
                        fields={ [
                            {
                                name: '_reset',
                                defaultValue: false,
                                render: ( field ) => (
                                    <>
                                        <Button
                                            variant="secondary"
                                            onClick={() => setIsDialogOpen(true)}
                                        > 
                                            { __( 'Reset Settings', 'product-input-fields-for-woocommerce' ) }
                                        </Button>
                                        <ConfirmDialog
                                            isOpen={isDialogOpen}
                                            cancelButtonText="Cancel"
                                            confirmButtonText="Reset"
                                            onCancel={() => { setIsDialogOpen(false) }}
                                            onConfirm={() => {
                                                resetSettings();
                                            }}
                                        >
                                            {__('Are you sure you want to reset to default settings?', 'product-input-fields-for-woocommerce')}
                                        </ConfirmDialog>
                                    </>
                                   
                                ),
                            },
                            {
                                name: 'ts_reset_tracking',
                                defaultValue: false,
                                render: ( field ) => (
                                    <Button
                                        variant="secondary"
                                        onClick={resetTracking}
                                    > { __( 'Reset Usage Tracking', 'product-input-fields-for-woocommerce' ) }</Button>
                                ),
                            },
                        ] }
                    />

                    <HStack spacing={3} expanded={false} justify="left">
                        <Button variant="primary" type="submit">{__('Save Changes', 'product-input-fields-for-woocommerce')}</Button>

                    </HStack>

                </VStack>
                
            </form>
            <style>
                    {`
                    tr:not(:last-child) td{
                        padding-bottom: 30px;
                    }
                    td:nth-child(2){
                        padding-left: 30px;
                    }
               `}
            </style>

            {showLoader ? <div className="pif_loader">< Spinner style={{ width: '30px', height: '30px' }
            } /></div > : ''}
        </VStack>
    );
}

export default withNotices(General);