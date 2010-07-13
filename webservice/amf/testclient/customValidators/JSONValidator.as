package customValidators
{
	import com.adobe.serialization.json.JSON;
	import com.adobe.serialization.json.JSONParseError;
	
	import mx.validators.ValidationResult;
	import mx.validators.Validator;
	
	import nl.demonsters.debugger.MonsterDebugger;

	public class JSONValidator extends Validator
	{
        // Define Array for the return value of doValidation().
        private var errors:Array;

		public function JSONValidator()
		{
			super();
		}
        
        override protected function doValidation(value:Object):Array {
        	var JSONstring:String = String(value);
            errors = [];
            if (JSONstring != ''){
				try {
					JSON.decode(JSONstring);
				} catch (err:Error){
					errors.push(new ValidationResult(true, null, "JSON decode failed", 
	                    "Not able to decode this JSON."));
				}
            }
            if (this.required && JSONstring == ''){
            	errors.push(new ValidationResult(true, null, "Required", 
	                    "You must enter a value for this argument - for a string argument an empty string can be entered as \"\" or you can disable an optional argument."));
            }
            return errors;
        }
		
	}
}