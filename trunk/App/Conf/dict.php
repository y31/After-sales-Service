<?php
/**
 * 字典表配置项
 */

return array(
		//订单状态
		'ORDER_STATE'			=>array(
								'STATE_PRE_DPF'=>10,//待派发（指定服务队）
								'STATE_PRE_PROCESS'=>20,//已派发[服务队=待处理]（系统发送邮件，只发送概要信息，详细信息要进系统）
								'STATE_EXECUTING'=>30,//处理中（服务队查看后即为已接收，状态改为处理中。）
								'STATE_PRE_SALE_CONFIRM'=>40,//完成待确认（服务队填写资料结单后）
								'STATE_FINISHI'=>50,//已确认（后台人员确认。抽查,填写用户评价）[终结][正常结算]
								'STATE_PRE_BREAK'=>60,//待中断[服务队=中断待确认]（服务队由于某种原因中断工单流程）
								'STATE_BREAK'=>70,//已中断（后台人员确认中断）[终结][无结算]
								'STATE_WG'=>80,//违规（后台人员确认违规）[终结][有惩罚结算]',
		
		),
		//创建时间
		'CREATED_TIME'			=>array(
			'10'=>'最近一周',
			'20'=>'最近一个月',
			'30'=>'最近三个月',
			'40'=>'半年',
			'50'=>'一年',
		),
		
		
);