import { useForm, Controller } from "react-hook-form";
import { 
    __experimentalVStack as VStack,
    __experimentalHStack as HStack,
    __experimentalHeading as Heading, 
    __experimentalText as Text, 
    __experimentalConfirmDialog as ConfirmDialog,
    Button,
    Tooltip,
    ToggleControl,
    Card,
    CardHeader,  
    CardBody,
    TabPanel,
    withNotices,
    Spinner,
} from "@wordpress/components";

import { __ } from '@wordpress/i18n';
import { FieldCard } from "../components";
import { FieldSettings, ConditionalLogic } from "./";
import { upload } from '@wordpress/icons';
import { useCallback, useState, useEffect, useMemo, useRef } from "@wordpress/element";
import { getFields, getField, updateField, addField, deleteField } from "../data/api";
import {FIELD_TYPES} from "../data/config";

function FieldBuilder({ noticeOperations, noticeUI, parentRef, settingsData, productID = null }) {
    const [fields, setFields] = useState(settingsData ?? []);
    const [globalFields, setGlobalFields] = useState([]);
    const [activeFieldId, setActiveFieldId] = useState(null);
    const [loading, setLoading] = useState(false);
    const [draggedId, setDraggedId] = useState(null);
    const [deleteId, setDeleteId] = useState(null);
    const [isDialogOpen, setIsDialogOpen] = useState(false);
    const componentRef = useRef(null);

    const isDirtyRef = useRef(false);
   
    const toBoolean = (value) => value === 'yes' || value === true;
    const getDefaultFieldValues = () => ({
        required: false,
        type: 'text',
        field_min_qty: 1,
        title: 'Input Field',
        placeholder: '',
        required_message: 'Field "%title%" is required!',
        class: '',
        style: '',
        input_restrictions_min: '',
        input_restrictions_maxlength: '',
        input_restrictions_pattern: '',
        type_file_accept:'.jpg,.jpeg,.png',
        type_file_wrong_type_msg: 'Wrong file type!',
        type_file_max_size_msg: 'File is too big!',
        type_datepicker_mindate: -365,
        type_datepicker_maxdate: 365,
        type_datepicker_yearrange: 'c-100:c+10',
        type_timepicker_format: 'hh:mm p',
        type_timepicker_interval: 15,
        type_checkbox_yes: 'Yes',
        type_checkbox_no: 'No',
        type_select_options: [],
    });

    const { control, handleSubmit, reset, watch, setValue, getValues, unregister, formState: { isDirty } } = useForm({
        defaultValues: settingsData[0] ?? getDefaultFieldValues(),
    });

    useEffect(() => {
        isDirtyRef.current = isDirty;
    }, [isDirty]);

    const onSubmit = async (data) => {
        await handleFieldsUpdate(data);
    };

    const onError = () => {
        console.log("Form Submission Error");
        noticeOperations.removeAllNotices();
        noticeOperations.createNotice({
            status: 'error',
            content: 'Please fill the required fields.',
        });
    }

    const fetchFields = async ( activeId = null ) => {
     //   setLoading(true);
    
        try {
            const data = await getFields(productID);
            
            setFields(data);

            if ( productID ) {
                const globalData = await getFields(null);
                setGlobalFields(globalData);
            }
    
            if (data.length) {
                const targetId = activeId ?? activeFieldId;
                const targetField = data.find(f => f.id === targetId);

                if (targetField) {
                    setActiveFieldId(targetField.id);
                    reset(targetField);
                } else {
                    setActiveFieldId(data[0].id);
                    reset(data[0]);
                }
            } else {
                handleAddField();
            }
    
        } catch (error) {
            console.error("Error fetching fields:", error);
        } finally {
           // setLoading(false);
        }
    };

    useEffect(() => {
        fetchFields();
    }, []);

    const handleAddField = () => {

        const tempId = `temp-${Date.now()}`;

        const newField = {
            id: tempId,
            type: "text",
            order: fields.length + 1,
            isTemp: true
        };

        setFields(prev => [...prev, newField]);
        setActiveFieldId(tempId);

        reset(getDefaultFieldValues());
    };

    const handleSelectField = async (id) => {
        // skip temp ids
        if (typeof id === "string" && id.startsWith("temp-")) {
            setActiveFieldId(id);
            reset(getDefaultFieldValues());
            return;
        }
        setLoading(true);

        try {
            if ( fields && fields.length > 0 ) {
                const existing = fields.find(f => f.id === id);
                if (existing) {
                    setActiveFieldId(id);
                    reset(existing);
                }
            } else {
                const field = await getField(id, productID);
                setActiveFieldId(id);
                reset(field);
            }
            
        } catch (error) {
            console.error("Error loading field:", error);
        } finally {
            setLoading(false);
        }
    };

    // Scroll up to the notice message when it appears
    useEffect(() => {
        if (noticeUI) {
            if ( parentRef?.current ) {
                parentRef.current.scrollIntoView({
                    behavior: "smooth",
                    block: "start"
                });
            } else if ( componentRef?.current ) {
                componentRef.current.scrollIntoView({
                    behavior: "smooth",
                    block: "start"
                });
            }
            
    
            const timer = setTimeout(() => {
                noticeOperations.removeAllNotices();
            }, 4000); // 4 seconds
    
            return () => clearTimeout(timer);
        }
    }, [noticeUI]);

    // ✅ show WP notice
    const showNotice = () => {

        const message = __('You have unsaved changes for Product Input Fields. Please save them before updating.', 'woocommerce-flexi-bogo');

        const noticesStore =
            window?.wp?.data?.dispatch?.("core/notices");

        // ✅ Try WP notices first
        if (noticesStore && noticesStore.createNotice) {

            noticesStore.createNotice(
                "error",
                message,
                { isDismissible: true }
            );

            return;
        }

        // ✅ fallback notice
        let notice =
            document.getElementById(
                "my-settings-notice"
            );

        if (!notice) {
            console.log('Creating fallback notice');
            notice = document.createElement("div");

            notice.id = "my-settings-notice";
            notice.className =
                "notice notice-error is-dismissible";

            notice.innerHTML =
                "<p>" + message + "</p>";

            const wrap =
                document.querySelector(
                    "#wpbody-content .wrap"
                ) ||
                document.querySelector(
                    ".wrap"
                );

            wrap?.prepend(notice);
        }

        // ✅ scroll to notice
        notice.scrollIntoView({
            behavior: "smooth",
            block: "start",
        });

    };

    useEffect(() => {

        if ( ! productID ) {
            return;
        }

        document.addEventListener('click', function (e) {

            if (e.target.closest('#publish, #save-post')) {
                if ( isDirtyRef.current) {
                    e.preventDefault();
                    showNotice();
                }
        
            }
        
        });
        
        document
            .getElementById('post')
            ?.addEventListener('submit', function () {
                if ( isDirtyRef.current) {
                    showNotice();
                }
        
            });

    }, []);

    const handleFieldsUpdate = async (data) => {  
        setLoading(true);
        try {
            if (activeFieldId && !String(activeFieldId).startsWith("temp")) {

                await updateField(activeFieldId, data, productID);

                noticeOperations.createNotice({
                    status: "success",
                    content: "Field updated successfully.",
                });

                await fetchFields( activeFieldId );

            } else {

                const newField = await addField(data, productID);

                setFields(prev =>
                    prev.map(f =>
                        f.id === activeFieldId ? { ...newField } : f
                    )
                );

                setActiveFieldId(newField.id);

                noticeOperations.createNotice({
                    status: "success",
                    content: "Field added successfully.",
                });

                await fetchFields( newField.id );
            }

        } catch (error) {
            noticeOperations.removeAllNotices();
            noticeOperations.createNotice({
                status: 'error',
                content: error.message,
            });
        } finally {
            setLoading(false);
        }
    };

    const handleFieldDelete = async (deleteId) => {

        try {
            await deleteField(deleteId, productID);

            setDeleteId(null);
            setIsDialogOpen(false);
            await fetchFields();
            noticeOperations.createNotice({
                status: "success",
                content: "Field deleted successfully.",
            });
        } catch (error) {
            noticeOperations.removeAllNotices();
            noticeOperations.createNotice({
                status: 'error',
                content: 'Failed to delete field.',
            });
        }

    }

    const duplicateField = async (fieldID) => {
        setLoading(true);
        try {
            const data = fields.find(f => f.id === fieldID);
            const newField = await addField(data, productID);

            setFields(prev => [...prev, newField]);

            setActiveFieldId(newField.id);

            noticeOperations.createNotice({
                status: "success",
                content: "Field added successfully.",
            });

            await fetchFields();

        } catch (error) {
            noticeOperations.removeAllNotices();
            noticeOperations.createNotice({
                status: 'error',
                content: `Failed to duplicate rule: ${error.message}`,
            });
        } finally {
            setLoading(false);
        }
    };

    const handleDragStart = (id) => {
        setDraggedId(id);
    };
      
    const handleDragOver = (e) => {
      e.preventDefault(); // required for drop
    };
    
    const handleDrop = async (targetId) => {
        if (!draggedId || draggedId === targetId) return;
        
        const updated = [...fields];
        const from = updated.findIndex(f => f.id === draggedId);
        const to = updated.findIndex(f => f.id === targetId);

        const [moved] = updated.splice(from, 1);
        updated.splice(to, 0, moved);

        const reordered = updated.map((f, i) => ({
            ...f,
            order: i + 1,
        }));

        setFields(reordered);
        setDraggedId(null);
        try {
            for (const field of reordered) {
                await updateField(field.id, field, productID);
            }
        } catch (error) {
            console.error('Failed to update rule order:', error);
        }
    };

    const sortedFields = useMemo(() => {
        return [...fields].sort((a, b) => (a.order || 0) - (b.order || 0));
    }, [fields]);

    const activeField = fields.find(f => f.id === activeFieldId);
    const activeIndex = fields.findIndex(f => f.id === activeFieldId);

    const tabs = [
        {
            name: 'settings',
            title: __('Field Settings', 'woocommerce-flexi-bogo'),
            component: <FieldSettings control={control} reset={reset} watch={watch} getValues={getValues} />
        },
        {
            name: 'conditions',
            title: __('Conditional Logic', 'woocommerce-flexi-bogo'),
            component: <ConditionalLogic control={control} reset={reset} watch={watch} setValue={setValue} activeID={activeFieldId} fieldsData={fields} productID={productID} globalFields={globalFields} />
        }
    ];

    const TruncatedWithTooltip = ({ text, limit = 15 }) => {
        if (!text) return null;

        if ( productID ) {
            limit = 10;
        }
    
        const short =
            text.length > limit
                ? text.substring(0, limit) + '...'
                : text;
    
        return (
            <Tooltip text={text}>
                <span className="pif-truncate">
                    {short}
                </span>
            </Tooltip>
        );
    };

    return (
        <>
        {
            !productID && <Text style={{ fontStyle: 'italic', padding:'0 0 10px 20px'}}>{__('Note: Fields added here will appear on all WooCommerce product pages across your store.', 'product-input-fields-for-woocommerce')}</Text>
        }
        <HStack alignment="start" ref={componentRef}>
            <VStack spacing={4} style={{flex: '1 1 0%', padding: '20px'}} className={'pif-field-builder'}>
                <Button variant="primary" onClick={handleAddField} style={{justifyContent: 'center'}}>Add Field</Button>
                {
                    sortedFields.map((field, index) => (
                        <div
                            key={field.id}
                            draggable
                            onDragStart={() => handleDragStart(field.id)}
                            onDragOver={handleDragOver}
                            onDrop={() => handleDrop(field.id)}
                        >
                        <FieldCard
                            title={<TruncatedWithTooltip text={`${field.title ? field.title : 'Input Field'}`} />}
                            subtitle={field.isTemp ? "Unsaved field" : FIELD_TYPES[field.type]?.label}
                            icon={FIELD_TYPES[field.type]?.icon || upload}
                            onCopy={() => duplicateField(field.id)}
                            onDelete={() => { 
                                setIsDialogOpen(true); 
                                setDeleteId(field.id) 
                            }}
                            onClick={() => handleSelectField(field.id) }
                            className={field.id === activeFieldId ? 'active-field-card' : ''}
                        />
                        </div>
                    ))
                }
                <ConfirmDialog
                        isOpen={isDialogOpen}
                        cancelButtonText="Cancel"
                        confirmButtonText="Delete"
                        onCancel={() => { setIsDialogOpen(false); setDeleteId(null) }}
                        onConfirm={() => {
                            handleFieldDelete(deleteId);
                            setDeleteId(null);
                        }}
                    >
                        {__('Are you sure you want to delete this field?', 'product-input-fields-for-woocommerce')}
                </ConfirmDialog>
            </VStack>
            <VStack spacing={4} style={{flex: 5}}>
                {noticeUI}

                <form onSubmit={handleSubmit(onSubmit, onError)}>
                    <Card style={{
                            boxShadow: 'none',
                            borderLeft: `2px solid #f2f2f2`,
                            borderRadius: 0,
                        }}>
                        <CardHeader>
                            <VStack spacing={4}>
                                <Heading level={ 4 }>{watch("title") || 'Input Field'}</Heading>
                                <HStack spacing={4}>
                                    <Controller
                                        name={'enabled'}
                                        control={control}
                                        defaultValue={false}
                                        render={({ field }) => (
                                            <ToggleControl
                                                label={__('Enable Field', 'woocommerce-flexi-bogo')}
                                                help={__('Enable this field to display on product page.', 'woocommerce-flexi-bogo')}
                                                value={field.value}
                                                checked={toBoolean(field.value)}
                                                __nextHasNoMarginBottom
                                                onChange={field.onChange}
                                                className="pif-field-label"
                                            />
                                        )}
                                    />

                                    <Controller
                                        name={'required'}
                                        control={control}
                                        defaultValue={false}
                                        render={({ field }) => (
                                            <ToggleControl
                                                label={__('Required Field', 'woocommerce-flexi-bogo')}
                                                help={__('Customers must fill this field.', 'woocommerce-flexi-bogo')}
                                                value={field.value}
                                                checked={toBoolean(field.value)}
                                                __nextHasNoMarginBottom
                                                onChange={field.onChange}
                                                disabled={ ! toBoolean( watch('enabled') ) }
                                                className="pif-field-label"
                                            />
                                        )}
                                    />      
                                    
                                </HStack>
                            </VStack>
                        </CardHeader>
                        <CardBody style={{padding: '0px'}}>
                            <VStack>
                                <TabPanel className="dashboard-tabs" tabs={tabs} initialTabName={'settings'}>
                                    {(tab) => tab.component}
                                </TabPanel>

                                <HStack spacing={3} expanded={false} justify="left" style={{margin:'10px 30px'}}>
                                    <Button variant="primary" type="submit">{__('Save Field', 'product-input-fields-for-woocommerce')}</Button>
                                </HStack>
                            </VStack>
                        </CardBody>
                    </Card>
                   
                </form>
            </VStack>
            {loading ? <div className="pif_loader">< Spinner style={{ width: '30px', height: '30px' }
            } /></div > : ''}
        </HStack>
        </>
    );
}

export default withNotices(FieldBuilder);