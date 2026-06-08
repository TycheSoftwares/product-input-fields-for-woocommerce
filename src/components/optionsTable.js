import { 
    Card, 
    CardHeader, 
    CardBody, 
    __experimentalVStack as VStack,
    __experimentalHStack as HStack,
    __experimentalHeading as Heading, 
    __experimentalText as Text, 
    __experimentalGrid as Grid,
    __experimentalNumberControl as NumberControl,
    __experimentalInputControl as InputControl,
    SelectControl,
    Button,
    FormFileUpload,
    DropZone
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useState, useEffect } from "@wordpress/element";

import { Controller, useFieldArray } from 'react-hook-form';
import { FIELD_RELATIONS } from '../data/config';
const OptionsTable = ({
    id,
    control,
    className = '',
    columns,
    templateColumns,
    optionFields,
    defaultValue = {}
}) => {

    const { fields, append, remove, update } = useFieldArray({
        name: id,
        control,
    });

    useEffect(() => {
        if (fields.length === 0 && defaultValue && Object.keys(defaultValue).length > 0) {
            append( defaultValue );
        }
    }, []);

    return (
        <>
            <Card>
                <CardHeader style={{ display: 'block', background: '#f9fafb'}}>
                    <Grid templateColumns={templateColumns}>
                        {
                            columns && columns.map((column, index) => (
                                <Text key={index}>{ column }</Text>
                            ))
                        }
                    </Grid>
                </CardHeader>
                <CardBody>
                    {
                        fields.map((item, index) => {
                            return (
                                <Grid id={item.id} key={item.id} templateColumns={templateColumns} templateRows={'1fr auto'}>
                                    {
                                        optionFields && optionFields.map((field, fieldIndex) => (
                                            field.showWhen === undefined || field.showWhen ? (
                                                <Controller
                                                    name={`${id}.${index}.${field.name}`}
                                                    control={control}
                                                    defaultValue={ field.defaultValue }
                                                    render={ ( { field: controllerField } ) =>
                                                        field.render( controllerField, index )
                                                    }
                                                />
                                        ) : null
                                            
                                        ))
                                    }
                                    <HStack>
                                        <Button icon={'trash'} isDestructive style={{ marginTop: 'auto' }} onClick={() => remove(index)} disabled={ 'type_conditional_options' === id ? true : false }/>
                                    </HStack>
                                </Grid>
                            )
                        })
                    }
                </CardBody>
            </Card>
            <HStack> 
                <Button className='pif-button-add-option' onClick={() => append( defaultValue )} disabled={ 'type_conditional_options' === id ? true : false }>{__('+ Add Option', 'product-input-fields-for-woocommerce')}</Button>
            </HStack>
        </>
    );
};

export default OptionsTable;