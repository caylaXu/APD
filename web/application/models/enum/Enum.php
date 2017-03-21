<?php

class Enum
{
	/**
	 * app登录用户类型
	 * 表名 PhoneLoginInfo
	 */
	const PhoneLoginTypeApd = 1;
	const PhoneLoginTypeCalendar = 2;

	/**
	 * 移动用户在线状态
	 * 表名 PhoneLoginInfo
	 */
	const  PhoneUserStatusOffline = 0;//登出
	const  PhoneUserStatusOnline = 1;//登录

	/**
	 * 推送类型
	 */
	const EnumPushType = 1;//被下线
	const EnumPushProjectManagerType = 2;//项目经理
	const EnumPushMemberType = 3;//项目成员
	const EnumPushTaskType = 5;//推送任务
}