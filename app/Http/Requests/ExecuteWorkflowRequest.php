namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExecuteWorkflowRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'workflow' => 'required|string',
            'input' => 'required|string',
            'steps' => 'array',
            'options' => 'array',
        ];
    }
}
