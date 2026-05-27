import { 
    Card, 
    CardHeader, 
    CardBody, 
    __experimentalVStack as VStack,
    __experimentalHeading as Heading, 
    __experimentalText as Text, 
    __experimentalTreeGrid as TreeGrid,
    __experimentalTreeGridRow as TreeGridRow,
    __experimentalTreeGridCell as TreeGridCell,
} from '@wordpress/components';
import { file } from '@wordpress/icons';
import { Controller } from 'react-hook-form';

const SettingsCardSection = ( {
    heading,
    subHeading = null,
    fields = [],
    display = true,
    control,
    className = '',
    spacing = 8,
} ) => {
    if ( !display ) {
        return null;
    }

    return (
        <Card className={className}>
            <CardHeader>
                <VStack spacing={ 2 }>
                    <Heading level={ 4 }>{ heading }</Heading>
                    { subHeading && (
                        <Text className="components-text">
                            { subHeading }
                        </Text>
                    ) }
                </VStack>
            </CardHeader>

            <CardBody>
                <TreeGrid style={{ borderCollapse: 'collapse' }}>
                    { fields.map( ( field, index ) => (
                        field.showWhen === undefined || field.showWhen ? (
                        <>
                            <TreeGridRow level={1} positionInSet={1} setSize={2}>
                                { field.label && ( <TreeGridCell style={{width:'30%'}}>
                                    {(props) => (
                                        <Text className={'pif-settings-label'}>{field.label}</Text>
                                    )}
                                </TreeGridCell>
                                ) }
                                <TreeGridCell>
                                    {(props) => (
                                        <Controller
                                            key={ index }
                                            name={ field.name }
                                            control={ control }
                                            defaultValue={ field.defaultValue }
                                            rules={ field.rules }
                                            render={ ( { field: controllerField, fieldState: { error } } ) =>
                                                field.render( controllerField, error )
                                            }
                                        />
                                    )}
                                </TreeGridCell>
                            </TreeGridRow>
                        </>
                        ) : null
                        
                    ) ) }
                </TreeGrid>
            </CardBody>
        </Card>
    );
};

export default SettingsCardSection;